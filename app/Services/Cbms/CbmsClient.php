<?php

namespace App\Services\Cbms;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Thin HTTP client for IRD's CBMS API, per the "Central Billing Monitoring
 * System API Documentation" (updated 2079 Ashoj 28 / 2022-10-14):
 *
 * 1. POST {bill_url}        - post an issued bill/invoice
 * 2. POST {bill_return_url} - post a credit note (sales return)
 *
 * Both endpoints take username/password/seller_pan as plain JSON fields in
 * the body (not HTTP auth headers) - to go live, seller_pan is the
 * taxpayer's PAN and username/password are the Taxpayer Portal login.
 */
class CbmsClient
{
    private const TIMEOUT_SECONDS = 20;

    public function postBill(array $payload): CbmsApiResponse
    {
        return $this->post(config('services.cbms.bill_url'), $payload, isCreditNote: false);
    }

    public function postCreditNote(array $payload): CbmsApiResponse
    {
        return $this->post(config('services.cbms.bill_return_url'), $payload, isCreditNote: true);
    }

    private function post(string $url, array $payload, bool $isCreditNote): CbmsApiResponse
    {
        try {
            $response = Http::timeout(self::TIMEOUT_SECONDS)
                ->acceptJson()
                ->post($url, $payload);

            return CbmsApiResponse::fromHttpResponse($response, $isCreditNote);
        } catch (\Throwable $e) {
            Log::error('CBMS API request failed', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return CbmsApiResponse::transportFailure($e->getMessage());
        }
    }
}
