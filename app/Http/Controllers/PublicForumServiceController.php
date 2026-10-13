<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PublicForumServiceController extends Controller
{
    public function news(Request $request): JsonResponse
    {
        $apiKey = env('NEWS_API_KEY');
        $country = $request->query('country', 'us');
        $category = $request->query('category', 'technology');

        if (! $apiKey) {
            return response()->json([
                'message' => 'NEWS_API_KEY is not configured.',
            ], 500);
        }

        $response = Http::timeout(20)->get('https://newsapi.org/v2/top-headlines', [
            'country' => $country,
            'category' => $category,
            'apiKey' => $apiKey,
        ]);

        if ($response->failed()) {
            return response()->json([
                'message' => 'News service unavailable.',
            ], 502);
        }

        $data = $response->json();

        return response()->json([
            'source' => 'NewsAPI',
            'items' => $data['articles'] ?? [],
        ]);
    }

    public function weather(Request $request): JsonResponse
    {
        $apiKey = env('OPENWEATHER_API_KEY');
        $city = $request->query('city', 'Belgrade');

        if (! $apiKey) {
            return response()->json([
                'message' => 'OPENWEATHER_API_KEY is not configured.',
            ], 500);
        }

        $response = Http::timeout(20)->get('https://api.openweathermap.org/data/2.5/weather', [
            'q' => $city,
            'appid' => $apiKey,
            'units' => 'metric',
            'lang' => 'en',
        ]);

        if ($response->failed()) {
            return response()->json([
                'message' => 'Weather service unavailable.',
            ], 502);
        }

        $data = $response->json();

        return response()->json([
            'source' => 'OpenWeatherMap',
            'city' => $data['name'] ?? $city,
            'temperature_c' => $data['main']['temp'] ?? null,
            'description' => $data['weather'][0]['description'] ?? null,
            'humidity' => $data['main']['humidity'] ?? null,
        ]);
    }
}
