<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebPushSubscriptionController extends Controller
{
    /**
     * Store or update a push subscription for the authenticated user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            "endpoint" => ["required", "string"],
            "keys.p256dh" => ["nullable", "string"],
            "keys.auth" => ["nullable", "string"],
            "content_encoding" => ["nullable", "string"],
        ]);

        $user = $request->user();

        if (! $user) {
            return response()->json(["message" => "Unauthenticated"], 401);
        }

        $user->updatePushSubscription(
            $request->string("endpoint")->toString(),
            $request->input("keys.p256dh"),
            $request->input("keys.auth"),
            $request->input("content_encoding")
        );

        return response()->json(["success" => true]);
    }

    /**
     * Delete a push subscription for the authenticated user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->validate([
            "endpoint" => ["required", "string"],
        ]);

        $user = $request->user();

        if (! $user) {
            return response()->json(["message" => "Unauthenticated"], 401);
        }

        $user->deletePushSubscription(
            $request->string("endpoint")->toString()
        );

        return response()->json(["success" => true]);
    }
}
