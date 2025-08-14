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

        $amount = $validated['amount'] ?? 2000; // default $10.00
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

        if ($sessionCustomerId) {
            try {
                // Try to retrieve the existing customer
                $customer = \Stripe\Customer::retrieve($sessionCustomerId);
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                // Customer doesn't exist, create a new one
                $customer = \Stripe\Customer::create([
                    'description' => 'Task Manager test customer',
                ]);
                $sessionCustomerId = $customer->id;
                session(['stripe_customer_id' => $sessionCustomerId]);
            }
        } else {
            // No customer in session, create a new one
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
            
            if ($customerId) {
                try {
                    // Try to retrieve the existing customer
                    $customer = \Stripe\Customer::retrieve($customerId);
                } catch (\Stripe\Exception\InvalidRequestException $e) {
                    // Customer doesn't exist, create a new one
                    $customer = \Stripe\Customer::create([
                        'description' => 'Task Manager test customer',
                    ]);
                    $customerId = $customer->id;
                    session(['stripe_customer_id' => $customerId]);
                }
            } else {
                // No customer in session, create a new one
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

    public function success(Request $request)
    {
        $paymentIntentId = $request->query('payment_intent');
        $setupIntentId = $request->query('setup_intent');
        
        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            
            if ($paymentIntentId) {
                $intent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
                $amount = $intent->amount / 100; // Convert from cents
                $currency = strtoupper($intent->currency);
                $status = $intent->status;
                $paymentMethod = $intent->payment_method;
                
                if ($paymentMethod) {
                    $pm = \Stripe\PaymentMethod::retrieve($paymentMethod);
                    $card = $pm->card;
                }
            } elseif ($setupIntentId) {
                $intent = \Stripe\SetupIntent::retrieve($setupIntentId);
                $status = $intent->status;
                $paymentMethod = $intent->payment_method;
                
                if ($paymentMethod) {
                    $pm = \Stripe\PaymentMethod::retrieve($paymentMethod);
                    $card = $pm->card;
                }
            }
            
            return view('stripe.success', compact('intent', 'amount', 'currency', 'status', 'card'));
        } catch (\Throwable $e) {
            return redirect()->route('stripe.index')->with('error', 'Invalid payment intent');
        }
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');
        
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sigHeader, $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }
        
        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                // Log successful payment
                \Log::info('Payment succeeded', [
                    'payment_intent_id' => $paymentIntent->id,
                    'amount' => $paymentIntent->amount,
                    'currency' => $paymentIntent->currency,
                ]);
                break;
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                // Log failed payment
                \Log::info('Payment failed', [
                    'payment_intent_id' => $paymentIntent->id,
                    'error' => $paymentIntent->last_payment_error,
                ]);
                break;
            case 'setup_intent.succeeded':
                $setupIntent = $event->data->object;
                // Log successful setup
                \Log::info('Setup succeeded', [
                    'setup_intent_id' => $setupIntent->id,
                    'payment_method_id' => $setupIntent->payment_method,
                ]);
                break;
            default:
                \Log::info('Received unknown event type ' . $event->type);
        }
        
        return response('Webhook received', 200);
    }
}


