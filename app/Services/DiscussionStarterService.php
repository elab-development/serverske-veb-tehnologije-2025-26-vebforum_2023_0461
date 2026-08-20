<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Fetches a random quote from the public DummyJSON API and reshapes it
 * into a ready-to-use forum discussion prompt.
 */
class DiscussionStarterService
{
    public function fetch(): ?array
    {
        try {
            $response = Http::timeout(3)->get('https://dummyjson.com/quotes/random');

            if ($response->failed()) {
                return null;
            }

            $quote = $response->json('quote');
            $author = $response->json('author');

            if (! $quote) {
                return null;
            }

            return [
                'quote' => $quote,
                'author' => $author,
                'suggested_title' => Str::limit($quote, 80),
                'word_count' => str_word_count($quote),
            ];
        } catch (Throwable $e) {
            Log::warning('Discussion starter service unavailable: ' . $e->getMessage());

            return null;
        }
    }
}
