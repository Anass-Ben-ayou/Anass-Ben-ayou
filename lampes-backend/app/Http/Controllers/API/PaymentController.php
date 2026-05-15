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
use App\Services\CheckoutGatewayManager;
use App\Support\SanitizesInput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    private const FREE_DELIVERY_THRESHOLD = 500.0;

    private const DELIVERY_FEE = 30.0;

    // Recoit le gestionnaire de passerelle de paiement configure.
    public function __construct(protected CheckoutGatewayManager $gatewayManager) {}

    // Cree une session de paiement fiable depuis le panier du client connecte.
    public function create(Request $request)
    {
        $request->merge($this->normalizedShippingInput($request));

        $validator = Validator::make($request->all(), [
            'adresse' => 'required|string|max:255',
            'ville' => 'required|string|max:100',
            'code_postal' => 'required|string|max:20',
            'pays' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        if (! $this->gatewayManager->cardEnabled()) {
            return response()->json([
                'success' => false,
                'message' => 'Le paiement en ligne n est pas configure pour le moment.',
            ], 422);
        }

        $panier = Panier::with(['lignePaniers.produit'])
            ->where('id_client', $request->user()->id_client)
            ->first();

        if (! $panier || $panier->lignePaniers->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Votre panier est vide.',
            ], 400);
        }

        try {
            $cartItems = $this->buildCartSnapshot($panier);
            $total = $this->calculateTrustedTotal($cartItems);
            $deliveryFee = $this->calculateDeliveryFee($cartItems);
            // Le montant et les lignes sont construits depuis le panier serveur avant l appel a Stripe.
            $gatewayCheckout = $this->gatewayManager->createCheckout([
                'amount' => $total,
                'currency' => config('payments.currency', 'MAD'),
                'items' => $cartItems,
                'delivery_fee' => $deliveryFee,
                'customer' => [
                    'id' => $request->user()->id_client,
                    'email' => $request->user()->email,
                    'first_name' => $request->user()->prenom ?? '',
                    'last_name' => $request->user()->nom ?? '',
                ],
                'shipping' => [
                    'adresse' => $request->adresse,
                    'ville' => $request->ville,
                    'code_postal' => $request->code_postal,
                    'pays' => $request->pays,
                    'country_code' => $this->normalizeCountryCode($request->pays),
                ],
            ]);

            PendingCheckout::updateOrCreate(
                ['stripe_session_id' => $gatewayCheckout['gateway_session_id']],
                [
                    'payment_gateway' => $gatewayCheckout['payment_gateway'],
                    'gateway_session_id' => $gatewayCheckout['gateway_session_id'],
                    'gateway_reference' => $gatewayCheckout['gateway_reference'],
                    'gateway_payload' => $gatewayCheckout['raw'] ?? null,
                    'id_client' => $request->user()->id_client,
                    'payload' => [
                        'adresse' => $request->adresse,
                        'ville' => $request->ville,
                        'code_postal' => $request->code_postal,
                        'pays' => $request->pays,
                        'methode_paiement' => 'carte',
                        'cart_items' => $cartItems,
                        'delivery_fee' => $deliveryFee,
                        'total' => $total,
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
                    'checkout_url' => $gatewayCheckout['checkout_url'],
                    'session_id' => $gatewayCheckout['gateway_session_id'],
                    'public_reference' => $gatewayCheckout['gateway_reference'],
                    'payment_gateway' => $gatewayCheckout['payment_gateway'],
                    'currency' => $gatewayCheckout['currency'],
                    'amount' => $gatewayCheckout['amount'],
                ],
            ]);
        } catch (\Throwable $exception) {
            Log::error('Payment checkout creation failed.', [
                'client_id' => $request->user()?->id_client,
                'error' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Impossible de creer la session de paiement.',
            ], 500);
        }
    }

    // Confirme une session de paiement appartenant au client connecte.
    public function confirm(Request $request)
    {
        $request->merge([
            'session_id' => SanitizesInput::plain($request->input('session_id'), 255),
            'public_reference' => SanitizesInput::plain($request->input('public_reference'), 255),
        ]);

        $validator = Validator::make($request->all(), [
            'session_id' => 'nullable|string',
            'public_reference' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        if (! $request->filled('session_id') && ! $request->filled('public_reference')) {
            return response()->json([
                'success' => false,
                'message' => 'Reference de paiement requise.',
            ], 422);
        }

        $pendingCheckout = PendingCheckout::query()
            ->where('id_client', $request->user()->id_client)
            ->where(function ($query) use ($request) {
                if ($request->filled('session_id')) {
                    $query->where('gateway_session_id', $request->session_id)
                        ->orWhere('stripe_session_id', $request->session_id);
                }

                if ($request->filled('public_reference')) {
                    $query->orWhere('gateway_reference', $request->public_reference);
                }
            })
            ->first();

        if (! $pendingCheckout) {
            return response()->json([
                'success' => false,
                'message' => 'Session de paiement introuvable.',
            ], 404);
        }

        try {
            $paymentDetails = $this->gatewayManager->fetchCheckoutStatus($pendingCheckout->toArray());
            $commande = $this->resolveCheckoutOutcome($pendingCheckout, $paymentDetails);

            if (! $commande) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le paiement est encore en attente.',
                    'data' => [
                        'payment_status' => $paymentDetails['status'] ?? 'pending',
                    ],
                ], 409);
            }

            return response()->json([
                'success' => true,
                'message' => 'Paiement confirme et commande finalisee.',
                'data' => $commande->load(['ligneCommandes.produit', 'paiement', 'livraison']),
            ]);
        } catch (\RuntimeException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 409);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la confirmation du paiement.',
            ], 500);
        }
    }

    // Redirige le client vers le frontend apres le retour de la passerelle.
    public function callback(Request $request)
    {
        $pendingCheckout = $this->locatePendingCheckout($request);

        if (! $pendingCheckout) {
            return redirect()->away($this->buildFrontendRedirect('failed', [
                'message' => 'session_introuvable',
            ]));
        }

        try {
            $paymentDetails = $this->gatewayManager->fetchCheckoutStatus($pendingCheckout->toArray());
            $commande = $this->resolveCheckoutOutcome($pendingCheckout, $paymentDetails);

            if ($commande) {
                return redirect()->away($this->buildFrontendRedirect('success', [
                    'order_id' => $commande->id_commande,
                    'session_id' => $pendingCheckout->gateway_session_id ?: $pendingCheckout->stripe_session_id,
                    'gateway' => $paymentDetails['payment_gateway'] ?? $pendingCheckout->payment_gateway,
                ]));
            }

            if (! empty($paymentDetails['pending'])) {
                return redirect()->away($this->buildFrontendRedirect('pending', [
                    'session_id' => $pendingCheckout->gateway_session_id ?: $pendingCheckout->stripe_session_id,
                    'gateway' => $paymentDetails['payment_gateway'] ?? $pendingCheckout->payment_gateway,
                ]));
            }

            return redirect()->away($this->buildFrontendRedirect('failed', [
                'session_id' => $pendingCheckout->gateway_session_id ?: $pendingCheckout->stripe_session_id,
                'gateway' => $paymentDetails['payment_gateway'] ?? $pendingCheckout->payment_gateway,
                'message' => $paymentDetails['status'] ?? 'payment_failed',
            ]));
        } catch (\Throwable $exception) {
            return redirect()->away($this->buildFrontendRedirect('failed', [
                'message' => 'callback_error',
            ]));
        }
    }

    // Recoit et verifie les mises a jour serveur a serveur de la passerelle.
    public function webhook(Request $request)
    {
        if (! $this->gatewayManager->verifyWebhook($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Webhook payment verification failed.',
            ], 400);
        }

        $reference = $this->gatewayManager->resolveWebhookReference($request);

        if (empty($reference['gateway_session_id']) && empty($reference['gateway_reference'])) {
            return response()->json(['success' => true]);
        }

        $pendingCheckout = PendingCheckout::query()
            ->where(function ($query) use ($reference) {
                if (! empty($reference['gateway_session_id'])) {
                    $query->orWhere('gateway_session_id', $reference['gateway_session_id'])
                        ->orWhere('stripe_session_id', $reference['gateway_session_id']);
                }

                if (! empty($reference['gateway_reference'])) {
                    $query->orWhere('gateway_reference', $reference['gateway_reference']);
                }
            })
            ->first();

        if (! $pendingCheckout) {
            return response()->json(['success' => true]);
        }

        try {
            $paymentDetails = $this->gatewayManager->fetchCheckoutStatus($pendingCheckout->toArray());
            $this->resolveCheckoutOutcome($pendingCheckout, $paymentDetails);
        } catch (\Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de traiter le webhook.',
            ], 500);
        }

        if (($pendingCheckout->payment_gateway ?? '') === 'payzone') {
            return response()->json([
                'status' => 'OK',
                'message' => 'Status recorded',
            ]);
        }

        return response()->json(['success' => true]);
    }

    // Transforme un paiement valide en commande une seule fois.
    protected function resolveCheckoutOutcome(PendingCheckout $pendingCheckout, array $paymentDetails): ?Commande
    {
        if (! empty($paymentDetails['pending'])) {
            return null;
        }

        if (empty($paymentDetails['paid'])) {
            throw new \RuntimeException('Le paiement n a pas ete approuve.');
        }

        $existingPayment = Paiement::query()
            ->where('reference_externe', $paymentDetails['session_id'] ?? $pendingCheckout->gateway_session_id)
            ->when(! empty($paymentDetails['transaction_id']), function ($query) use ($paymentDetails) {
                $query->orWhere('transaction_id', $paymentDetails['transaction_id']);
            })
            ->first();

        if ($existingPayment) {
            return $existingPayment->commande()->firstOrFail();
        }

        return DB::transaction(function () use ($pendingCheckout, $paymentDetails) {
            $commande = $this->createOrderFromPayload(
                $pendingCheckout->id_client,
                $pendingCheckout->payload,
                $paymentDetails
            );

            $pendingCheckout->completed_at = now();
            $pendingCheckout->gateway_payload = $paymentDetails['raw'] ?? $pendingCheckout->gateway_payload;
            $pendingCheckout->save();

            return $commande;
        });
    }

    // Cree la commande, le paiement, la livraison, ajuste le stock et nettoie le panier.
    protected function createOrderFromPayload(int $clientId, array $payload, array $paymentDetails): Commande
    {
        $cartItems = $payload['cart_items'] ?? [];
        $total = $this->calculateTrustedTotal($cartItems);

        if (round((float) ($payload['total'] ?? 0), 2) !== round($total, 2)) {
            throw new \RuntimeException('Le total du panier est invalide.');
        }

        if (isset($paymentDetails['amount']) && round((float) $paymentDetails['amount'], 2) !== round($total, 2)) {
            throw new \RuntimeException('Le montant confirme par la passerelle ne correspond pas au panier.');
        }

        $commande = Commande::create([
            'date_commande' => now(),
            'statut' => 'payee',
            'payment_status' => 'paid',
            'total' => $total,
            'currency' => strtoupper((string) ($payload['currency'] ?? config('payments.currency', 'MAD'))),
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
            'montant' => $total,
            'currency' => strtoupper((string) ($payload['currency'] ?? config('payments.currency', 'MAD'))),
            'methode' => 'carte',
            'payment_gateway' => $paymentDetails['payment_gateway'] ?? config('payments.provider', 'stripe'),
            'transaction_id' => $paymentDetails['transaction_id'] ?? null,
            'payment_token' => $paymentDetails['payment_token'] ?? null,
            'statut' => 'valide',
            'payment_status' => 'paid',
            'card_brand' => $paymentDetails['card_brand'] ?? null,
            'card_last4' => $paymentDetails['card_last4'] ?? null,
            'card_country' => $paymentDetails['card_country'] ?? null,
            'gateway_response' => $paymentDetails['raw'] ?? null,
            'reference_externe' => $paymentDetails['session_id'] ?? null,
            'id_commande' => $commande->id_commande,
            'id_client' => $clientId,
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

    // Trouve un paiement en attente depuis les champs de retour de la passerelle.
    protected function locatePendingCheckout(Request $request): ?PendingCheckout
    {
        $sessionId = SanitizesInput::plain(
            $request->query('session_id')
                ?: $request->input('merchantToken')
                ?: $request->input('session_id'),
            255
        );
        $publicReference = SanitizesInput::plain(
            $request->input('customer')
                ?: $request->query('customer')
                ?: $request->input('public_reference'),
            255
        );

        if (! $sessionId && ! $publicReference) {
            return null;
        }

        return PendingCheckout::query()
            ->where(function ($query) use ($sessionId, $publicReference) {
                if ($sessionId) {
                    $query->orWhere('gateway_session_id', $sessionId)
                        ->orWhere('stripe_session_id', $sessionId);
                }

                if ($publicReference) {
                    $query->orWhere('gateway_reference', $publicReference);
                }
            })
            ->first();
    }

    // Construit les lignes du panier depuis les produits stockes sur le serveur.
    protected function buildCartSnapshot(Panier $panier): array
    {
        return $panier->lignePaniers->map(function ($ligne) {
            if (! $ligne->produit || $ligne->produit->stock < $ligne->quantite) {
                $name = $ligne->produit?->nom ?: 'ce produit';
                throw new \RuntimeException("Stock insuffisant pour {$name}");
            }

            return [
                'id_produit' => $ligne->id_produit,
                'nom' => $ligne->produit->nom,
                'quantite' => (int) $ligne->quantite,
                'prix_unitaire' => (float) $ligne->produit->prix,
            ];
        })->values()->all();
    }

    // Calcule le montant total fiable avec la livraison.
    protected function calculateTrustedTotal(array $cartItems): float
    {
        return round($this->calculateCartSubtotal($cartItems) + $this->calculateDeliveryFee($cartItems), 2);
    }

    // Calcule le total des produits sans livraison.
    protected function calculateCartSubtotal(array $cartItems): float
    {
        return round(collect($cartItems)->sum(
            fn ($item) => ((float) $item['prix_unitaire']) * ((int) $item['quantite'])
        ), 2);
    }

    // Applique la regle des frais de livraison au panier.
    protected function calculateDeliveryFee(array $cartItems): float
    {
        return $this->calculateCartSubtotal($cartItems) >= self::FREE_DELIVERY_THRESHOLD
            ? 0.0
            : self::DELIVERY_FEE;
    }

    // Convertit les noms de pays courants en codes pour la passerelle.
    protected function normalizeCountryCode(string $country): string
    {
        return match (mb_strtolower(trim($country))) {
            'maroc', 'morocco' => 'MA',
            'france' => 'FR',
            'spain', 'espagne' => 'ES',
            default => 'MA',
        };
    }

    // Construit l URL de redirection frontend avec des parametres propres.
    protected function buildFrontendRedirect(string $state, array $params = []): string
    {
        $baseUrl = match ($state) {
            'success' => (string) config('payments.frontend_success_url'),
            'pending' => (string) config('payments.frontend_pending_url'),
            default => (string) config('payments.frontend_cancel_url'),
        };

        $query = http_build_query(array_filter($params, fn ($value) => $value !== null && $value !== ''));

        return $query ? rtrim($baseUrl, '/').'?'.$query : $baseUrl;
    }

    // Nettoie les champs de livraison avant validation et sauvegarde.
    protected function normalizedShippingInput(Request $request): array
    {
        return [
            'adresse' => SanitizesInput::plain($request->input('adresse'), 255),
            'ville' => SanitizesInput::plain($request->input('ville'), 100),
            'code_postal' => SanitizesInput::plain($request->input('code_postal'), 20),
            'pays' => SanitizesInput::plain($request->input('pays'), 100),
        ];
    }
}
