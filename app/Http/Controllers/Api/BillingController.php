<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CheckoutRequest;
use App\Models\Organization;
use App\Services\ProjectLimitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function __construct(private ProjectLimitService $projectLimitService) {}

    public function status(Request $request): JsonResponse
    {
        /** @var Organization $organization */
        $organization = $request->user()->organization;

        return response()->json($this->projectLimitService->statusFor($organization));
    }

    public function checkout(CheckoutRequest $request): JsonResponse
    {
        /** @var Organization $organization */
        $organization = $request->user()->organization;

        $this->authorize('manageBilling', $organization);

        if ($organization->subscribed('default')) {
            return response()->json([
                'message' => 'Organization already has an active subscription.',
            ], 422);
        }

        $priceId = $request->priceId();

        if (empty($priceId)) {
            return response()->json([
                'message' => 'Stripe price ID is not configured for the selected plan.',
            ], 500);
        }

        $checkout = $organization
            ->newSubscription('default', $priceId)
            ->checkout([
                'success_url' => config('app.url').'/billing/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => config('app.url').'/billing/cancel',
            ]);

        return response()->json([
            'checkout_url' => $checkout->url,
        ]);
    }
}
