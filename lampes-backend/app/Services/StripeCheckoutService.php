<?php

namespace App\Services;

use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\Response;
use RuntimeException;

class StripeCheckoutService
{
    public function __construct(protected HttpFactory $http) {}

    public function createCheckoutSession(array $payload): array
    {
        $response = $this->request()->post('/checkout/sessions', $payload);

        return $this->decodeResponse($response);
    }

    public function retrieveCheckoutSession(string $sessionId): array
    {
        $response = $this->request()->get("/checkout/sessions/{$sessionId}");

        return $this->decodeResponse($response);
    }

    public function retrieveCheckoutSessionWithExpansions(string $sessionId, array $expands = []): array
    {
        $query = [];

        foreach (array_values($expands) as $index => $expand) {
            $query["expand[{$index}]"] = $expand;
        }

        $response = $this->request()->get("/checkout/sessions/{$sessionId}", $query);

        return $this->decodeResponse($response);
    }

    protected function request()
    {
        $secretKey = (string) config('payments.stripe.secret_key');

        if ($secretKey === '') {
            throw new RuntimeException('Stripe secret key is not configured.');
        }

        return $this->http
            ->asForm()
            ->baseUrl((string) config('payments.stripe.api_base', 'https://api.stripe.com/v1'))
            ->withBasicAuth($secretKey, '')
            ->withHeaders([
                'Stripe-Version' => (string) config('payments.stripe.api_version', '2025-02-24.acacia'),
            ]);
    }

    protected function decodeResponse(Response $response): array
    {
        if ($response->failed()) {
            $message = $response->json('error.message') ?: 'Stripe request failed.';
            throw new RuntimeException($message);
        }

        return $response->json();
    }
}
