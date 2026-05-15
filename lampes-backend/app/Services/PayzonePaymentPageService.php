<?php

namespace App\Services;

use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\Response;
use RuntimeException;

class PayzonePaymentPageService
{
    public function __construct(protected HttpFactory $http) {}

    public function preparePayment(array $payload): array
    {
        $response = $this->request()->post('/payment/prepare', $payload);

        return $this->decodeResponse($response);
    }

    public function fetchPaymentStatus(string $merchantToken): array
    {
        $response = $this->request()->get("/payment/{$merchantToken}/status", [
            'apiVersion' => (string) config('payments.payzone.api_version', '002.70'),
        ]);

        return $this->decodeResponse($response);
    }

    public function buildCheckoutUrl(string $customerToken): string
    {
        return rtrim((string) config('payments.payzone.redirect_base_url', 'https://paiement.payzone.ma/payment'), '/').'/'.$customerToken;
    }

    protected function request()
    {
        $originator = trim((string) config('payments.merchant_id'));
        $password = trim((string) config('payments.api_key'));

        if ($originator === '' || $password === '') {
            throw new RuntimeException('Payzone merchant credentials are not configured.');
        }

        return $this->http
            ->acceptJson()
            ->asJson()
            ->baseUrl((string) config('payments.payzone.base_url', 'https://paiement.payzone.ma'))
            ->withBasicAuth($originator, $password);
    }

    protected function decodeResponse(Response $response): array
    {
        $data = $response->json();

        if ($response->failed()) {
            $message = is_array($data) ? ($data['message'] ?? 'Payzone request failed.') : 'Payzone request failed.';
            throw new RuntimeException($message);
        }

        if (($data['code'] ?? null) && (string) $data['code'] !== '200') {
            throw new RuntimeException((string) ($data['message'] ?? 'Payzone request failed.'));
        }

        return is_array($data) ? $data : [];
    }
}
