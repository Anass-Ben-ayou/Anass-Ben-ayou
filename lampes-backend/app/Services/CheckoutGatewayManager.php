<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use RuntimeException;

class CheckoutGatewayManager
{
    public function __construct(
        protected StripeCheckoutService $stripe,
        protected PayzonePaymentPageService $payzone,
    ) {}

    public function provider(): string
    {
        return Str::lower((string) config('payments.provider', 'stripe'));
    }

    public function cardEnabled(): bool
    {
        if ($this->demoEnabled()) {
            return true;
        }

        return match ($this->provider()) {
            'payzone' => trim((string) config('payments.merchant_id')) !== '' && trim((string) config('payments.api_key')) !== '',
            'stripe' => $this->stripeTestKeysConfigured(),
            default => false,
        };
    }

    public function createCheckout(array $context): array
    {
        if ($this->demoEnabled() && ! $this->hasRealGatewayCredentials()) {
            return $this->createDemoCheckout($context);
        }

        return match ($this->provider()) {
            'payzone' => $this->createPayzoneCheckout($context),
            'stripe' => $this->createStripeCheckout($context),
            default => throw new RuntimeException('Unsupported payment gateway configured.'),
        };
    }

    public function fetchCheckoutStatus(array $pendingCheckout): array
    {
        return match ($pendingCheckout['payment_gateway'] ?? $this->provider()) {
            'demo' => $this->fetchDemoStatus($pendingCheckout),
            'payzone' => $this->fetchPayzoneStatus($pendingCheckout),
            'stripe' => $this->fetchStripeStatus($pendingCheckout),
            default => throw new RuntimeException('Unsupported payment gateway configured.'),
        };
    }

    public function verifyWebhook(Request $request): bool
    {
        return match ($this->resolveWebhookProvider($request)) {
            'stripe' => $this->verifyStripeWebhook($request),
            'payzone' => true,
            default => false,
        };
    }

    public function resolveWebhookReference(Request $request): array
    {
        $provider = $this->resolveWebhookProvider($request);

        if ($provider === 'stripe') {
            $event = json_decode($request->getContent(), true);

            return [
                'provider' => 'stripe',
                'gateway_session_id' => $event['data']['object']['id'] ?? null,
                'gateway_reference' => null,
            ];
        }

        $payload = $request->json()->all();

        return [
            'provider' => 'payzone',
            'gateway_session_id' => $payload['merchantToken'] ?? $request->input('merchantToken'),
            'gateway_reference' => $payload['customerToken'] ?? $request->input('customer'),
        ];
    }

    public function paymentConfig(): array
    {
        $provider = $this->provider();
        $enabled = $this->cardEnabled();

        return [
            'card_enabled' => $enabled,
            'card_mode' => $this->demoEnabled() && ! $this->hasRealGatewayCredentials() ? 'demo' : ($enabled ? $provider : 'disabled'),
            'card_label' => $provider === 'payzone'
                ? 'Visa / Mastercard / cartes bancaires marocaines'
                : 'Visa / Mastercard',
            'card_message' => $this->demoEnabled() && ! $this->hasRealGatewayCredentials()
                ? 'Mode test local actif. Aucun vrai debit bancaire ne sera effectue.'
                : ($enabled
                ? ($provider === 'payzone'
                    ? 'Paiement securise via Payzone.'
                    : 'Paiement test securise via Stripe Checkout.')
                : 'Le paiement en ligne est indisponible. Configurez les cles Stripe de test sur le serveur.'),
            'payment_gateway' => $this->demoEnabled() && ! $this->hasRealGatewayCredentials() ? 'demo' : $provider,
            'stripe_publishable_key' => $provider === 'stripe' ? (string) config('payments.stripe.publishable_key') : null,
            'currency' => strtoupper((string) config('payments.currency', 'MAD')),
        ];
    }

    protected function createDemoCheckout(array $context): array
    {
        $sessionId = 'demo_checkout_'.Str::lower(Str::random(24));
        $successUrl = rtrim((string) config('payments.frontend_success_url'), '/');

        return [
            'payment_gateway' => 'demo',
            'checkout_url' => $successUrl.'?session_id='.$sessionId,
            'gateway_session_id' => $sessionId,
            'gateway_reference' => $sessionId,
            'amount' => $context['amount'],
            'currency' => Str::upper((string) ($context['currency'] ?? config('payments.currency', 'MAD'))),
            'raw' => [
                'id' => $sessionId,
                'payment_status' => 'paid',
                'demo_mode' => true,
            ],
        ];
    }

    protected function createStripeCheckout(array $context): array
    {
        // Stripe Checkout is created only on the Laravel API so STRIPE_SECRET_KEY never reaches React.
        $successUrl = rtrim((string) config('payments.callback_url'), '/').'?gateway=stripe&session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl = rtrim((string) config('payments.frontend_cancel_url'), '/');
        $currency = Str::lower((string) ($context['currency'] ?? config('payments.currency', 'MAD')));
        $lineItems = [];

        foreach (array_values($context['items']) as $index => $item) {
            $lineItems["line_items[{$index}][quantity]"] = $item['quantite'];
            $lineItems["line_items[{$index}][price_data][currency]"] = $currency;
            $lineItems["line_items[{$index}][price_data][unit_amount]"] = (int) round($item['prix_unitaire'] * 100);
            $lineItems["line_items[{$index}][price_data][product_data][name]"] = $item['nom'];
        }

        $deliveryFee = (float) ($context['delivery_fee'] ?? 0);
        if ($deliveryFee > 0) {
            $shippingIndex = count($context['items']);
            $lineItems["line_items[{$shippingIndex}][quantity]"] = 1;
            $lineItems["line_items[{$shippingIndex}][price_data][currency]"] = $currency;
            $lineItems["line_items[{$shippingIndex}][price_data][unit_amount]"] = (int) round($deliveryFee * 100);
            $lineItems["line_items[{$shippingIndex}][price_data][product_data][name]"] = 'Livraison';
        }

        $session = $this->stripe->createCheckoutSession([
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'customer_email' => $context['customer']['email'],
            'client_reference_id' => (string) $context['customer']['id'],
            'payment_method_types[0]' => 'card',
            'metadata[id_client]' => (string) $context['customer']['id'],
            'metadata[currency]' => Str::upper((string) ($context['currency'] ?? config('payments.currency', 'MAD'))),
            'metadata[address_city]' => $context['shipping']['ville'],
            ...$lineItems,
        ]);

        return [
            'payment_gateway' => 'stripe',
            'checkout_url' => $session['url'] ?? null,
            'gateway_session_id' => $session['id'],
            'gateway_reference' => $session['id'],
            'amount' => $context['amount'],
            'currency' => Str::upper((string) ($context['currency'] ?? config('payments.currency', 'MAD'))),
            'raw' => $session,
        ];
    }

    protected function createPayzoneCheckout(array $context): array
    {
        $merchantOrderId = 'SL-'.now()->format('YmdHis').'-'.$context['customer']['id'].'-'.Str::upper(Str::random(6));
        $callbackUrl = rtrim((string) config('payments.callback_url'), '/');

        $response = $this->payzone->preparePayment([
            'apiVersion' => (string) config('payments.payzone.api_version', '002.70'),
            'paymentMethod' => 'CreditCard',
            'paymentMode' => 'single',
            'operation' => 'sale',
            'currency' => Str::upper((string) ($context['currency'] ?? config('payments.currency', 'MAD'))),
            'amount' => (int) round($context['amount'] * 100),
            'order' => [
                'id' => $merchantOrderId,
                'description' => 'Commande SolarLight '.$merchantOrderId,
                'shippingType' => 'physicalGoods',
                'type' => 'goodsService',
            ],
            'shopper' => [
                'id' => (string) $context['customer']['id'],
                'firstName' => $context['customer']['first_name'] ?: 'Client',
                'lastName' => $context['customer']['last_name'] ?: 'SolarLight',
                'email' => $context['customer']['email'],
                'address1' => $context['shipping']['adresse'],
                'zipcode' => $context['shipping']['code_postal'],
                'city' => $context['shipping']['ville'],
                'countryCode' => strtoupper((string) ($context['shipping']['country_code'] ?? 'MA')),
            ],
            'shipping' => [
                'name' => trim(($context['customer']['first_name'] ?: '').' '.($context['customer']['last_name'] ?: '')) ?: 'Client SolarLight',
                'address1' => $context['shipping']['adresse'],
                'zipcode' => $context['shipping']['code_postal'],
                'city' => $context['shipping']['ville'],
                'countryCode' => strtoupper((string) ($context['shipping']['country_code'] ?? 'MA')),
            ],
            'ctrlRedirectURL' => $callbackUrl,
            'ctrlCallbackURL' => rtrim((string) config('app.url'), '/').'/api/checkout/payment-webhook?gateway=payzone',
            'ctrlCustomData' => json_encode([
                'client_id' => $context['customer']['id'],
                'cart_hash' => sha1(json_encode($context['items'])),
            ]),
        ]);

        return [
            'payment_gateway' => 'payzone',
            'checkout_url' => $this->payzone->buildCheckoutUrl((string) $response['customerToken']),
            'gateway_session_id' => $response['merchantToken'],
            'gateway_reference' => $response['customerToken'],
            'amount' => $context['amount'],
            'currency' => Str::upper((string) ($context['currency'] ?? config('payments.currency', 'MAD'))),
            'raw' => $response,
        ];
    }

    protected function fetchStripeStatus(array $pendingCheckout): array
    {
        // The order is finalized only after Laravel verifies the Stripe Checkout session status server-side.
        $session = $this->stripe->retrieveCheckoutSessionWithExpansions(
            (string) ($pendingCheckout['gateway_session_id'] ?? $pendingCheckout['stripe_session_id']),
            ['payment_intent.latest_charge', 'payment_intent.payment_method']
        );

        $paymentIntent = Arr::get($session, 'payment_intent', []);
        $latestCharge = Arr::get($paymentIntent, 'latest_charge', []);
        $card = Arr::get($latestCharge, 'payment_method_details.card', []);

        return [
            'payment_gateway' => 'stripe',
            'session_id' => $session['id'],
            'transaction_id' => $latestCharge['id'] ?? ($paymentIntent['id'] ?? $session['id']),
            'payment_token' => $paymentIntent['id'] ?? null,
            'card_brand' => $card['brand'] ?? null,
            'card_last4' => $card['last4'] ?? null,
            'card_country' => $card['country'] ?? null,
            'status' => $session['payment_status'] ?? 'unpaid',
            'amount' => isset($session['amount_total']) ? round(((int) $session['amount_total']) / 100, 2) : null,
            'currency' => strtoupper((string) ($session['currency'] ?? config('payments.currency', 'MAD'))),
            'raw' => $session,
            'paid' => ($session['payment_status'] ?? null) === 'paid',
            'pending' => in_array(($session['payment_status'] ?? ''), ['open', 'processing', 'unpaid'], true),
        ];
    }

    protected function fetchDemoStatus(array $pendingCheckout): array
    {
        $sessionId = (string) ($pendingCheckout['gateway_session_id'] ?? $pendingCheckout['stripe_session_id'] ?? '');

        return [
            'payment_gateway' => 'demo',
            'session_id' => $sessionId,
            'transaction_id' => 'demo_txn_'.Str::lower(Str::random(16)),
            'payment_token' => $sessionId,
            'card_brand' => 'visa',
            'card_last4' => '4242',
            'card_country' => 'MA',
            'status' => 'paid',
            'amount' => $pendingCheckout['payload']['total'] ?? null,
            'currency' => strtoupper((string) ($pendingCheckout['payload']['currency'] ?? config('payments.currency', 'MAD'))),
            'raw' => [
                'id' => $sessionId,
                'payment_status' => 'paid',
                'demo_mode' => true,
            ],
            'paid' => true,
            'pending' => false,
        ];
    }

    protected function fetchPayzoneStatus(array $pendingCheckout): array
    {
        $merchantToken = (string) ($pendingCheckout['gateway_session_id'] ?? '');
        $status = $this->payzone->fetchPaymentStatus($merchantToken);
        $lastTransaction = collect($status['transactions'] ?? [])->last() ?: [];
        $paymentMeanInfo = $status['paymentMeanInfo'] ?? [];
        $maskedCard = (string) ($paymentMeanInfo['cardNumber'] ?? $paymentMeanInfo['maskedPan'] ?? '');
        $statusLabel = Str::lower((string) ($status['status'] ?? 'pending'));
        $resultCode = (string) ($lastTransaction['errorCode'] ?? $lastTransaction['resultCode'] ?? $status['errorCode'] ?? '');

        return [
            'payment_gateway' => 'payzone',
            'session_id' => $merchantToken,
            'transaction_id' => $lastTransaction['transactionID'] ?? $lastTransaction['id'] ?? $merchantToken,
            'payment_token' => $merchantToken,
            'card_brand' => $paymentMeanInfo['cardType'] ?? $paymentMeanInfo['brand'] ?? 'card',
            'card_last4' => $this->extractLast4($maskedCard),
            'card_country' => $paymentMeanInfo['country'] ?? $paymentMeanInfo['countryCode'] ?? 'MA',
            'status' => $status['status'] ?? 'Pending',
            'amount' => isset($status['amount']) ? round(((int) $status['amount']) / 100, 2) : null,
            'currency' => strtoupper((string) ($status['currency'] ?? config('payments.currency', 'MAD'))),
            'raw' => $status,
            'paid' => $resultCode === '000' || in_array($statusLabel, ['authorized', 'captured', 'paid'], true),
            'pending' => in_array($statusLabel, ['pending', 'not processed'], true),
        ];
    }

    protected function resolveWebhookProvider(Request $request): string
    {
        $header = Str::lower((string) $request->header('Stripe-Signature'));

        if ($header !== '') {
            return 'stripe';
        }

        return Str::lower((string) ($request->query('gateway') ?: $request->input('gateway') ?: $this->provider()));
    }

    protected function verifyStripeWebhook(Request $request): bool
    {
        $payload = $request->getContent();
        $signatureHeader = (string) $request->header('Stripe-Signature', '');
        $endpointSecret = (string) config('payments.stripe.webhook_secret');

        if ($payload === '' || $signatureHeader === '' || $endpointSecret === '') {
            return false;
        }

        $parts = collect(explode(',', $signatureHeader))
            ->mapWithKeys(function ($chunk) {
                [$key, $value] = array_pad(explode('=', trim($chunk), 2), 2, null);

                return [$key => $value];
            });

        $timestamp = $parts->get('t');
        $signature = $parts->get('v1');

        if (! $timestamp || ! $signature || abs(time() - (int) $timestamp) > 300) {
            return false;
        }

        return hash_equals(
            hash_hmac('sha256', "{$timestamp}.{$payload}", $endpointSecret),
            $signature
        );
    }

    protected function extractLast4(string $maskedCard): ?string
    {
        $digits = preg_replace('/\D+/', '', $maskedCard);

        if (! $digits || strlen($digits) < 4) {
            return null;
        }

        return substr($digits, -4);
    }

    protected function demoEnabled(): bool
    {
        return (bool) config('payments.demo.enabled', false);
    }

    protected function hasRealGatewayCredentials(): bool
    {
        return match ($this->provider()) {
            'payzone' => trim((string) config('payments.merchant_id')) !== '' && trim((string) config('payments.api_key')) !== '',
            'stripe' => $this->stripeTestKeysConfigured(),
            default => false,
        };
    }

    protected function stripeTestKeysConfigured(): bool
    {
        $secretKey = trim((string) config('payments.stripe.secret_key'));
        $publishableKey = trim((string) config('payments.stripe.publishable_key'));

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
}
