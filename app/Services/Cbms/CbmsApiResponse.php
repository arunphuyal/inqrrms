<?php

namespace App\Services\Cbms;

/**
 * Normalizes a CBMS API response into a typed result.
 *
 * The documented response is a bare code (e.g. "200", "101") in the response
 * body rather than a JSON-wrapped object, so this also tolerates a few
 * plausible variants (plain string/int body, or a JSON object carrying the
 * code under a "code"-like key).
 */
class CbmsApiResponse
{
    /** Response codes shared by both /api/bill and /api/billreturn. */
    public const MESSAGES = [
        '100' => 'API credentials do not match',
        '101' => 'Bill already exists',
        '102' => 'Exception while saving bill details - check model fields and values',
        '103' => 'Unknown exception - check API URL and model fields and values',
        '104' => 'Model invalid',
        '200' => 'Success',
    ];

    /** Credit-note-specific override of code 101 and its extra code 105. */
    public const CREDIT_NOTE_MESSAGES = [
        '101' => 'Bill does not exist',
        '105' => 'Bill does not exist (for Sales Return)',
    ];

    public function __construct(
        public readonly bool $httpOk,
        public readonly ?int $httpStatus,
        public readonly string $code,
        public readonly string $message,
        public readonly mixed $raw,
    ) {
    }

    public function isSuccess(): bool
    {
        return $this->httpOk && $this->code === '200';
    }

    public static function fromHttpResponse(\Illuminate\Http\Client\Response $response, bool $isCreditNote = false): self
    {
        $code = self::extractCode($response->body());
        $messages = $isCreditNote ? array_merge(self::MESSAGES, self::CREDIT_NOTE_MESSAGES) : self::MESSAGES;
        $message = $messages[$code] ?? ('Unrecognized response code: ' . $code);

        return new self(
            httpOk: $response->successful(),
            httpStatus: $response->status(),
            code: $code,
            message: $message,
            raw: $response->body(),
        );
    }

    public static function transportFailure(string $reason): self
    {
        return new self(
            httpOk: false,
            httpStatus: null,
            code: 'transport_error',
            message: $reason,
            raw: null,
        );
    }

    private static function extractCode(string $body): string
    {
        $decoded = json_decode($body, true);

        if (is_scalar($decoded)) {
            return trim((string) $decoded);
        }

        if (is_array($decoded)) {
            foreach (['code', 'Code', 'responseCode', 'ResponseCode', 'status', 'Status'] as $key) {
                if (isset($decoded[$key]) && is_scalar($decoded[$key])) {
                    return trim((string) $decoded[$key]);
                }
            }
        }

        if (preg_match('/-?\d+/', $body, $matches)) {
            return $matches[0];
        }

        return trim($body);
    }
}
