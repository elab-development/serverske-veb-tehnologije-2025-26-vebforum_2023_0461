<?php

namespace App\Http\Controllers;

use App\Services\DiscussionStarterService;

class DiscussionStarterController extends Controller
{
    public function show(DiscussionStarterService $service)
    {
        $starter = $service->fetch();

        if (! $starter) {
            return response()->json([
                'message' => 'Discussion starter service is currently unavailable.',
            ], 503);
        }

        return response()->json(['data' => $starter]);
    }
}
