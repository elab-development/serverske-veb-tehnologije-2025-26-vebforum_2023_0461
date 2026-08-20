<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Calls the public vector.profanity.dev web service to flag profane content.
 * Fails open (treats content as clean) if the external service is
 * unreachable, so a third-party outage never blocks posting.
 */
class ProfanityFilter
{
    public function containsProfanity(string $text): bool
    {
        if (trim($text) === '') {
            return false;
        }

        try {
            $response = Http::timeout(3)->post('https://vector.profanity.dev', [
                'message' => $text,
            ]);

            if ($response->failed()) {
                return false;
            }

            return (bool) $response->json('isProfanity', false);
        } catch (Throwable $e) {
            Log::warning('Profanity filter service unavailable: ' . $e->getMessage());

            return false;
        }
    }
}
