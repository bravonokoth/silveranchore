<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Events\NotificationSent;
use App\Mail\OrderConfirmed;
use App\Notifications\OrderPlaced;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Illuminate\Support\Str;
use Unicodeveloper\Paystack\Facades\Paystack;

class PaymentController extends Controller
{
    public function initialize(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'amount' => 'required|numeric|min:0.5',
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($validated['order_id']);

        $paymentData = [
            'amount' => $validated['amount'] * 100,
            'reference' => Paystack::genTranxRef(),
            'email' => $validated['email'],
            'callback_url' => route('payment.callback'),
            'metadata' => [
                'order_id' => $order->id,
                'customer_email' => $validated['email'],
                'custom_fields' => [
                    [
                        'display_name' => 'Order ID',
                        'variable_name' => 'order_id',
                        'value' => $order->id,
                    ],
                ],
            ],
        ];

        try {
            $order->update([
                'payment_reference' => $paymentData['reference'],
                'payment_method' => 'paystack'
            ]);
            Log::info('Paystack initialized for Order #' . $order->id, $paymentData);
            return Paystack::getAuthorizationUrl($paymentData)->redirectNow();
        } catch (\Exception $e) {
            Log::error('Paystack Init Error: ' . $e->getMessage(), ['order_id' => $order->id]);
            return back()->with('error', 'Unable to initialize payment. Please try again.');
        }
    }

    public function callback()
    {
        Log::info('🔥🔥🔥 PAYMENT CALLBACK HIT 🔥🔥🔥');
        
        try {
            $paymentDetails = Paystack::getPaymentData();
            Log::info('=== PAYSTACK CALLBACK ===', $paymentDetails);

            $orderId = $paymentDetails['data']['metadata']['order_id'] ?? null;
            if (!$orderId) {
                Log::error('❌ Order ID not found in payment metadata');
                throw new \Exception('Order ID not found in payment metadata');
            }

            Log::info("📦 Loading Order #{$orderId} with relationships...");
            $order = Order::with(['shippingAddress', 'items.product'])->findOrFail($orderId);
            Log::info("✅ Order loaded", ['order_id' => $order->id, 'current_status' => $order->payment_status]);

            if ($paymentDetails['data']['status'] === 'success') {
                Log::info('✅ Payment status is SUCCESS');
                
                // Skip if already paid
                if ($order->payment_status === 'paid') {
                    Log::info("⚠️ Order #{$order->id} already marked as paid. Skipping duplicate processing.");
                    return redirect()->route('orders.success', $order)
                        ->with('success', 'Payment already confirmed.');
                }
                
                $updated = $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing',
                    'payment_reference' => $paymentDetails['data']['reference'],
                    'payment_method' => 'paystack',
                    'paid_at' => now(),
                ]);

                if (!$updated) {
                    throw new \Exception('Failed to update order payment status');
                }
                
                Log::info("✅ Order #{$order->id} updated to PAID");

                // Determine email
                $email = $order->email 
                    ?? ($order->shippingAddress->email ?? $paymentDetails['data']['customer']['email'] ?? null);

                Log::info("📧 Customer email: {$email}");

                // Create notification using Laravel's notification system
                $message = "Payment for order #{$order->id} completed successfully.";
                
                try {
                    // Store notification in database
                    \DB::table('notifications')->insert([
                        'id' => (string) Str::uuid(),
                        'type' => OrderStatusUpdated::class,
                        'notifiable_type' => $order->user_id ? 'App\Models\User' : null,
                        'notifiable_id' => $order->user_id,
                        'data' => json_encode([
                            'message' => $message,
                            'email' => $email,
                            'session_id' => $order->session_id,
                            'order_id' => $order->id,
                            'status' => 'paid',
                        ]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Fire event for real-time notification
                    event(new NotificationSent($message, $email, $order->user_id, $order->session_id));
                    
                    Log::info("✅ Customer notification created");
                } catch (\Exception $e) {
                    Log::error("❌ Failed to create customer notification: " . $e->getMessage());
                }

                // SEND ORDER CONFIRMED EMAIL TO CUSTOMER
                if ($email) {
                    try {
                        Mail::to($email)->queue(new OrderConfirmed($order));
                        Log::info("✅ Customer confirmation email queued to {$email}");
                    } catch (\Exception $e) {
                        Log::error("❌ Failed to queue customer confirmation email: " . $e->getMessage());
                    }
                }

                // NOTIFY ADMINS ABOUT NEW PAID ORDER
                Log::info('🔔 Starting admin notification process...');
                try {
                    // Get admins using a safer approach
                    $admins = $this->getAdminUsers();
                    
                    Log::info("👥 Found {$admins->count()} admin(s)", [
                        'admin_emails' => $admins->pluck('email')->toArray()
                    ]);
                    
                    if ($admins->isEmpty()) {
                        Log::warning("⚠️ No admins found! Please run: php artisan db:seed --class=RoleSeeder");
                    } else {
                        Log::info("📤 Sending OrderPlaced notification to admins...");
                        NotificationFacade::send($admins, new OrderPlaced($order));
                        Log::info("✅✅✅ Admin notifications sent successfully to {$admins->count()} admin(s)");
                    }
                } catch (\Exception $e) {
                    Log::error("❌ Failed to notify admins: " . $e->getMessage());
                    Log::error("Stack trace: " . $e->getTraceAsString());
                }

                Log::info("🎉 Payment callback completed successfully for Order #{$order->id}");
                
                return redirect()->route('orders.success', $order)
                    ->with('success', 'Payment successful! Order confirmed.');
            }

            // Payment failed
            Log::warning("⚠️ Payment failed for Order #{$order->id}");
            $order->update([
                'payment_status' => 'failed',
                'status' => 'canceled',
            ]);

            Log::warning("Order #{$order->id} payment failed.", [
                'reason' => $paymentDetails['data']['gateway_response'] ?? 'Unknown',
            ]);

            return redirect()->route('checkout.index')
                ->with('error', 'Payment failed. Please try again.');

        } catch (\Exception $e) {
            Log::error('❌❌❌ PAYSTACK CALLBACK ERROR: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->route('home')
                ->with('error', 'Payment verification failed. Please contact support.');
        }
    }

    public function webhook(Request $request)
    {
        Log::info('🔥 WEBHOOK ENDPOINT HIT');
        
        $signature = $request->header('x-paystack-signature');
        $payload = $request->getContent();

        if ($signature !== hash_hmac('sha512', $payload, config('paystack.secretKey'))) {
            Log::warning('Invalid Paystack webhook signature');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $event = json_decode($payload, true);
        Log::info('=== PAYSTACK WEBHOOK ===', $event);

        if ($event['event'] === 'charge.success') {
            $orderId = $event['data']['metadata']['order_id'] ?? null;
            if ($orderId) {
                $order = Order::with(['shippingAddress', 'items.product'])->find($orderId);
                if ($order && $order->payment_status !== 'paid') {
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'processing',
                        'payment_reference' => $event['data']['reference'],
                        'payment_method' => 'paystack',
                        'paid_at' => now(),
                    ]);

                    $email = $order->email 
                        ?? ($order->shippingAddress->email ?? $event['data']['customer']['email'] ?? null);

                    // Customer notification using proper Laravel notification system
                    $message = "Payment for order #{$order->id} completed successfully via webhook.";
                    
                    try {
                        \DB::table('notifications')->insert([
                            'id' => (string) Str::uuid(),
                            'type' => OrderStatusUpdated::class,
                            'notifiable_type' => $order->user_id ? 'App\Models\User' : null,
                            'notifiable_id' => $order->user_id,
                            'data' => json_encode([
                                'message' => $message,
                                'email' => $email,
                                'session_id' => $order->session_id,
                                'order_id' => $order->id,
                                'status' => 'paid',
                            ]),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        event(new NotificationSent($message, $email, $order->user_id, $order->session_id));
                    } catch (\Exception $e) {
                        Log::error("Webhook notification creation failed: " . $e->getMessage());
                    }

                    // QUEUE EMAIL TO CUSTOMER
                    if ($email) {
                        try {
                            Mail::to($email)->queue(new OrderConfirmed($order));
                            Log::info("Order confirmation email queued via webhook to {$email}");
                        } catch (\Exception $e) {
                            Log::error("Webhook email queue failed: " . $e->getMessage());
                        }
                    }

                    // NOTIFY ADMINS
                    Log::info('🔔 (Webhook) Starting admin notification...');
                    try {
                        $admins = $this->getAdminUsers();
                        
                        if ($admins->isNotEmpty()) {
                            NotificationFacade::send($admins, new OrderPlaced($order));
                            Log::info("✅ Admin notification sent via webhook for Order #{$order->id} to {$admins->count()} admin(s)");
                        } else {
                            Log::warning("No admins found to notify for Order #{$order->id} (webhook)");
                        }
                    } catch (\Exception $e) {
                        Log::error("Failed to notify admins via webhook: " . $e->getMessage());
                    }

                    Log::info("Order #{$order->id} updated via webhook.", [
                        'payment_reference' => $event['data']['reference'],
                    ]);
                }
            }
        }

        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Get admin users safely, handling missing roles
     */
    protected function getAdminUsers()
    {
        try {
            // Try to get users with admin or super-admin roles (note: hyphen, not underscore)
            return User::role(['admin', 'super-admin'])->get();
        } catch (\Spatie\Permission\Exceptions\RoleDoesNotExist $e) {
            Log::warning("Role doesn't exist: " . $e->getMessage());
            
            // Fallback: Try each role individually
            $admins = collect();
            
            try {
                $admins = $admins->merge(User::role('admin')->get());
            } catch (\Exception $e) {
                // admin role doesn't exist
            }
            
            try {
                $admins = $admins->merge(User::role('super-admin')->get());
            } catch (\Exception $e) {
                // super-admin role doesn't exist
            }
            
            // Last resort: Get users who have is_admin flag or similar
            if ($admins->isEmpty()) {
                // Adjust this based on your User model structure
                $admins = User::where('is_admin', true)->get();
            }
            
            return $admins;
        }
    }
}