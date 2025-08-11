<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StripeController extends Controller
{
    public function index()
    {
        return view('stripe.index');
    }

    public function createPaymentIntent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['nullable', 'integer', 'min:50'], // in cents; min $0.50
            'currency' => ['nullable', 'string']
        ]);

        $amount = $validated['amount'] ?? 1000; // default $10.00
        $currency = $validated['currency'] ?? 'usd';

        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            $intent = \Stripe\PaymentIntent::create([
                'amount' => $amount,
                'currency' => $currency,
                'automatic_payment_methods' => ['enabled' => true],
                'metadata' => [
                    'app' => 'task-manager',
                    'env' => app()->environment(),
                ],
            ]);

            return response()->json([
                'clientSecret' => $intent->client_secret,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function paymentMethods()
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $sessionCustomerId = session('stripe_customer_id');

        if (!$sessionCustomerId) {
            $customer = \Stripe\Customer::create([
                'description' => 'Task Manager test customer',
            ]);
            $sessionCustomerId = $customer->id;
            session(['stripe_customer_id' => $sessionCustomerId]);
        }

        $methods = \Stripe\PaymentMethod::all([
            'customer' => $sessionCustomerId,
            'type' => 'card',
        ]);

        return view('stripe.methods', [
            'customerId' => $sessionCustomerId,
            'paymentMethods' => $methods->data,
        ]);
    }

    public function createSetupIntent(): JsonResponse
    {
        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            $customerId = session('stripe_customer_id');
            if (!$customerId) {
                $customer = \Stripe\Customer::create([
                    'description' => 'Task Manager test customer',
                ]);
                $customerId = $customer->id;
                session(['stripe_customer_id' => $customerId]);
            }

            $intent = \Stripe\SetupIntent::create([
                'customer' => $customerId,
                'automatic_payment_methods' => ['enabled' => true],
                'metadata' => [
                    'app' => 'task-manager',
                    'env' => app()->environment(),
                ],
            ]);

            return response()->json([
                'clientSecret' => $intent->client_secret,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function detachPaymentMethod(string $paymentMethodId): JsonResponse
    {
        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            $pm = \Stripe\PaymentMethod::retrieve($paymentMethodId);
            $pm->detach();
            return response()->json(['ok' => true]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}


