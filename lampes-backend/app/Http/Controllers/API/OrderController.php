<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\LigneCommande;
use App\Models\LignePanier;
use App\Models\Livraison;
use App\Models\Paiement;
use App\Models\Panier;
use App\Models\PendingCheckout;
use App\Models\Produit;
use App\Services\StripeCheckoutService;
use App\Support\SanitizesInput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    private const FREE_DELIVERY_THRESHOLD = 500.0;

    private const DELIVERY_FEE = 30.0;

    public function __construct(protected StripeCheckoutService $stripeCheckoutService) {}

    public function paymentConfig()
    {
        $provider = strtolower((string) config('payments.provider', 'stripe'));
        $realGatewayEnabled = match ($provider) {
            'payzone' => trim((string) config('payments.merchant_id')) !== '' && trim((string) config('payments.api_key')) !== '',
            'stripe' => $this->stripeIsConfigured(),
            default => false,
        };
        $demoEnabled = (bool) config('payments.demo.enabled', false);
        $cardEnabled = $realGatewayEnabled || $demoEnabled;
        $mode = $realGatewayEnabled ? $provider : ($demoEnabled ? 'demo' : 'disabled');

        return response()->json([
            'success' => true,
            'data' => [
                'card_enabled' => $cardEnabled,
                'card_mode' => $mode,
                'card_label' => $provider === 'payzone'
                    ? 'Visa / Mastercard / cartes bancaires marocaines'
                    : 'Visa / Mastercard',
                'card_message' => $demoEnabled && ! $realGatewayEnabled
                    ? 'Mode test local actif. Aucun vrai debit bancaire ne sera effectue.'
                    : ($cardEnabled
                    ? ($provider === 'payzone'
                        ? 'Paiement securise via Payzone.'
                        : 'Paiement test securise via Stripe Checkout.')
                    : 'Le paiement par carte est temporairement indisponible. Configurez les cles Stripe de test.'),
                'payment_gateway' => $mode === 'demo' ? 'demo' : $provider,
                'stripe_publishable_key' => $provider === 'stripe' ? (string) config('payments.stripe.publishable_key') : null,
                'currency' => strtoupper((string) config('payments.currency', 'MAD')),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->merge($this->normalizedShippingInput($request));

        $validator = Validator::make($request->all(), $this->orderRules());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $panier = Panier::with(['lignePaniers.produit'])
            ->where('id_client', $request->user()->id_client)
            ->first();

        if (! $panier || $panier->lignePaniers->count() === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Votre panier est vide',
            ], 400);
        }

        foreach ($panier->lignePaniers as $ligne) {
            if ($ligne->produit->stock < $ligne->quantite) {
                return response()->json([
                    'success' => false,
                    'message' => "Stock insuffisant pour {$ligne->produit->nom}",
                ], 400);
            }
        }

        if ($request->methode_paiement === 'carte') {
            // Card orders are sent to Stripe Checkout in test mode; raw card details are never accepted here.
            return $this->createCardCheckout($request, $panier);
        }

        DB::beginTransaction();

        try {
            $cartItems = $this->buildCartSnapshot($panier);

            $commande = $this->createOrderFromPayload($request->user()->id_client, [
                'adresse' => $request->adresse,
                'ville' => $request->ville,
                'code_postal' => $request->code_postal,
                'pays' => $request->pays,
                'methode_paiement' => $request->methode_paiement,
                'cart_items' => $cartItems,
                'delivery_fee' => $this->calculateDeliveryFee($cartItems),
                'total' => $this->calculateTrustedTotal($cartItems),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Commande creee avec succes',
                'data' => $commande->load(['ligneCommandes.produit', 'paiement', 'livraison']),
            ], 201);
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la creation de la commande',
            ], 500);
        }
    }

    public function confirmCardCheckout(Request $request)
    {
        $request->merge([
            'session_id' => SanitizesInput::plain($request->input('session_id'), 255),
        ]);

        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $pendingCheckout = PendingCheckout::where('stripe_session_id', $request->session_id)
                ->where('id_client', $request->user()->id_client)
                ->first();

            if (! $pendingCheckout) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session de paiement introuvable.',
                ], 404);
            }

            $existingPayment = Paiement::where('reference_externe', $request->session_id)->first();

            if ($existingPayment) {
                return response()->json([
                    'success' => true,
                    'message' => 'Paiement deja confirme.',
                    'data' => $existingPayment->commande()->with(['ligneCommandes.produit', 'paiement', 'livraison'])->first(),
                ]);
            }

            $session = $this->stripeCheckoutService->retrieveCheckoutSession($request->session_id);

            if (($session['payment_status'] ?? null) !== 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Le paiement carte n a pas ete confirme.',
                    'data' => [
                        'payment_status' => $session['payment_status'] ?? 'unknown',
                    ],
                ], 409);
            }

            DB::beginTransaction();

            $commande = $this->createOrderFromPayload($request->user()->id_client, $pendingCheckout->payload, $request->session_id);

            $pendingCheckout->completed_at = now();
            $pendingCheckout->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Paiement confirme et commande creee avec succes.',
                'data' => $commande->load(['ligneCommandes.produit', 'paiement', 'livraison']),
            ]);
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la confirmation du paiement.',
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $commandes = Commande::with(['ligneCommandes.produit', 'paiement', 'livraison'])
                ->where('id_client', $request->user()->id_client)
                ->orderBy('date_commande', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => $commandes,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des commandes',
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $commande = Commande::with(['ligneCommandes.produit.categorie', 'paiement', 'livraison'])
                ->where('id_client', $request->user()->id_client)
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $commande,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Commande non trouvee',
            ], 404);
        }
    }

    public function cancel(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $commande = Commande::with('ligneCommandes')->findOrFail($id);

            if ($commande->id_client !== $request->user()->id_client) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Vous n etes pas autorise a annuler cette commande',
                ], 403);
            }

            if ($commande->statut !== 'en_attente') {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Cette commande ne peut pas etre annulee',
                ], 400);
            }

            foreach ($commande->ligneCommandes as $ligne) {
                $produit = Produit::find($ligne->id_produit);
                $produit->stock += $ligne->quantite;
                $produit->save();
            }

            $commande->statut = 'annulee';
            $commande->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Commande annulee avec succes',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l annulation',
            ], 500);
        }
    }

    protected function createCardCheckout(Request $request, Panier $panier)
    {
        if (! $this->stripeIsConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'Le paiement par carte est indisponible tant que STRIPE_SECRET_KEY n est pas configure sur le serveur.',
            ], 422);
        }

        try {
            $cartItems = $this->buildCartSnapshot($panier);
            // Stripe must return through Laravel first so the API can verify payment and create the order.
            $successUrl = rtrim((string) config('payments.callback_url'), '/').'?gateway=stripe&session_id={CHECKOUT_SESSION_ID}';
            $cancelUrl = rtrim((string) config('payments.frontend_cancel_url'), '/');
            $currency = strtolower((string) config('payments.currency', 'mad'));

            $lineItems = [];

            foreach ($cartItems as $index => $item) {
                $lineItems["line_items[{$index}][quantity]"] = $item['quantite'];
                $lineItems["line_items[{$index}][price_data][currency]"] = $currency;
                $lineItems["line_items[{$index}][price_data][unit_amount]"] = (int) round($item['prix_unitaire'] * 100);
                $lineItems["line_items[{$index}][price_data][product_data][name]"] = $item['nom'];
            }

            $deliveryFee = $this->calculateDeliveryFee($cartItems);
            if ($deliveryFee > 0) {
                $shippingIndex = count($cartItems);
                $lineItems["line_items[{$shippingIndex}][quantity]"] = 1;
                $lineItems["line_items[{$shippingIndex}][price_data][currency]"] = $currency;
                $lineItems["line_items[{$shippingIndex}][price_data][unit_amount]"] = (int) round($deliveryFee * 100);
                $lineItems["line_items[{$shippingIndex}][price_data][product_data][name]"] = 'Livraison';
            }

            $payload = [
                'mode' => 'payment',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'customer_email' => $request->user()->email,
                'client_reference_id' => (string) $request->user()->id_client,
                'payment_method_types[0]' => 'card',
                'metadata[id_client]' => (string) $request->user()->id_client,
                ...$lineItems,
            ];

            $session = $this->stripeCheckoutService->createCheckoutSession($payload);

            PendingCheckout::updateOrCreate(
                ['stripe_session_id' => $session['id']],
                [
                    'payment_gateway' => 'stripe',
                    'gateway_session_id' => $session['id'],
                    'gateway_reference' => $session['id'],
                    'gateway_payload' => $session,
                    'id_client' => $request->user()->id_client,
                    'payload' => [
                        'adresse' => $request->adresse,
                        'ville' => $request->ville,
                        'code_postal' => $request->code_postal,
                        'pays' => $request->pays,
                        'methode_paiement' => 'carte',
                        'cart_items' => $cartItems,
                        'delivery_fee' => $deliveryFee,
                        'total' => $this->calculateTrustedTotal($cartItems),
                        'currency' => strtoupper((string) config('payments.currency', 'MAD')),
                    ],
                    'expires_at' => now()->addHours(24),
                    'completed_at' => null,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Session de paiement creee.',
                'data' => [
                    'checkout_url' => $session['url'],
                    'session_id' => $session['id'],
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 503);
        }
    }

    protected function createOrderFromPayload(int $clientId, array $payload, ?string $externalReference = null): Commande
    {
        $cartItems = $payload['cart_items'] ?? [];

        $commande = Commande::create([
            'date_commande' => now(),
            'statut' => ($payload['methode_paiement'] ?? 'livraison') === 'carte' ? 'payee' : 'en_attente',
            'total' => $payload['total'],
            'id_client' => $clientId,
        ]);

        foreach ($cartItems as $item) {
            $produit = Produit::whereKey($item['id_produit'])->lockForUpdate()->firstOrFail();

            if ($produit->stock < $item['quantite']) {
                throw new \RuntimeException("Stock insuffisant pour {$produit->nom}");
            }

            LigneCommande::create([
                'quantite' => $item['quantite'],
                'prix_unitaire' => $item['prix_unitaire'],
                'id_commande' => $commande->id_commande,
                'id_produit' => $item['id_produit'],
            ]);

            $produit->stock -= $item['quantite'];
            $produit->save();
        }

        Paiement::create([
            'date_paiement' => now(),
            'montant' => $payload['total'],
            'methode' => $payload['methode_paiement'],
            'statut' => ($payload['methode_paiement'] ?? 'livraison') === 'livraison' ? 'en_attente' : 'valide',
            'reference_externe' => $externalReference,
            'id_commande' => $commande->id_commande,
        ]);

        Livraison::create([
            'adresse' => $payload['adresse'],
            'ville' => $payload['ville'],
            'code_postal' => $payload['code_postal'],
            'pays' => $payload['pays'],
            'statut' => 'en_attente',
            'id_commande' => $commande->id_commande,
        ]);

        $panier = Panier::where('id_client', $clientId)->first();

        if ($panier) {
            $productIds = collect($cartItems)->pluck('id_produit')->all();
            LignePanier::where('id_panier', $panier->id_panier)
                ->whereIn('id_produit', $productIds)
                ->delete();
        }

        return $commande;
    }

    protected function buildCartSnapshot(Panier $panier): array
    {
        return $panier->lignePaniers->map(function ($ligne) {
            return [
                'id_produit' => $ligne->id_produit,
                'nom' => $ligne->produit->nom,
                'quantite' => (int) $ligne->quantite,
                'prix_unitaire' => (float) $ligne->produit->prix,
            ];
        })->values()->all();
    }

    protected function calculateTrustedTotal(array $cartItems): float
    {
        return round($this->calculateCartSubtotal($cartItems) + $this->calculateDeliveryFee($cartItems), 2);
    }

    protected function calculateCartSubtotal(array $cartItems): float
    {
        return round(collect($cartItems)->sum(
            fn ($item) => ((float) $item['prix_unitaire']) * ((int) $item['quantite'])
        ), 2);
    }

    protected function calculateDeliveryFee(array $cartItems): float
    {
        return $this->calculateCartSubtotal($cartItems) >= self::FREE_DELIVERY_THRESHOLD
            ? 0.0
            : self::DELIVERY_FEE;
    }

    protected function orderRules(): array
    {
        return [
            'adresse' => 'required|string|max:255',
            'ville' => 'required|string|max:100',
            'code_postal' => 'required|string|max:20',
            'pays' => 'required|string|max:100',
            'methode_paiement' => 'required|in:carte,livraison',
        ];
    }

    protected function stripeIsConfigured(): bool
    {
        $secretKey = trim((string) config('payments.stripe.secret_key', ''));
        $publishableKey = trim((string) config('payments.stripe.publishable_key', ''));

        if ($secretKey === '' || $publishableKey === '') {
            return false;
        }

        if (! (bool) config('payments.stripe.test_mode_only', true)) {
            return true;
        }

        return Str::startsWith($secretKey, 'sk_test_')
            && Str::startsWith($publishableKey, 'pk_test_')
            && ! Str::contains($secretKey.$publishableKey, 'xxxxxxxx');
    }

    protected function demoCardEnabled(): bool
    {
        return (bool) config('payments.demo.enabled', false);
    }

    protected function paymentMode(): string
    {
        if ($this->stripeIsConfigured()) {
            return 'stripe';
        }

        if ($this->demoCardEnabled()) {
            return 'demo';
        }

        return 'disabled';
    }

    protected function normalizedShippingInput(Request $request): array
    {
        return [
            'adresse' => SanitizesInput::plain($request->input('adresse'), 255),
            'ville' => SanitizesInput::plain($request->input('ville'), 100),
            'code_postal' => SanitizesInput::plain($request->input('code_postal'), 20),
            'pays' => SanitizesInput::plain($request->input('pays'), 100),
            'methode_paiement' => SanitizesInput::plain($request->input('methode_paiement'), 20),
        ];
    }
}
