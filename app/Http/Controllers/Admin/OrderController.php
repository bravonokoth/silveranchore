<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Notification;
use App\Events\NotificationSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:admin|super_admin']);
    }

    public function index()
    {
        $orders = Order::with(['user', 'items.product', 'shippingAddress', 'billingAddress'])
            ->latest()
            ->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'shippingAddress', 'billingAddress']);
        return view('admin.orders.show', compact('order'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $orders = Order::with(['user', 'items.product', 'shippingAddress', 'billingAddress'])
            ->where('id', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhereHas('user', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->orWhereHas('shippingAddress', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('phone_number', 'like', "%{$query}%");
            })
            ->orWhereHas('items.product', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->orWhere('status', 'like', "%{$query}%")
            ->orWhere('payment_status', 'like', "%{$query}%")
            ->orWhere('total', 'like', "%{$query}%")
            ->latest()
            ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function edit(Order $order)
    {
        $order->load(['user', 'items.product', 'shippingAddress', 'billingAddress']);
        return view('admin.orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,canceled',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            // Shipping address fields
            'shipping_address.name' => 'required|string|max:255',
            'shipping_address.phone_number' => 'required|string|max:20',
            'shipping_address.email' => 'required|email|max:255',
            'shipping_address.line1' => 'required|string|max:255',
            'shipping_address.line2' => 'nullable|string|max:255',
            'shipping_address.city' => 'required|string|max:100',
            'shipping_address.state' => 'nullable|string|max:100',
            'shipping_address.postal_code' => 'nullable|string|max:20',
            'shipping_address.country' => 'required|string|max:100',
        ]);

        $oldStatus = $order->status;

        // Update or create shipping address
        $shippingAddressData = $validated['shipping_address'];
        $shippingAddressData['user_id'] = $order->user_id;
        $shippingAddressData['session_id'] = $order->session_id;
        $shippingAddressData['type'] = 'shipping';

        if ($order->shippingAddress) {
            $order->shippingAddress->update($shippingAddressData);
        } else {
            $shippingAddress = \App\Models\Address::create($shippingAddressData);
            $order->shipping_address_id = $shippingAddress->id;
        }

        // Update order
        $order->update([
            'status' => $validated['status'],
            'payment_status' => $validated['payment_status'],
        ]);

        if ($oldStatus !== $validated['status']) {
            $message = "Your order #{$order->id} status changed to {$validated['status']}.";
            Notification::create([
                'user_id' => $order->user_id,
                'session_id' => $order->session_id,
                'email' => $order->email,
                'message' => $message,
                'is_read' => false,
            ]);

            event(new NotificationSent(
                $message,
                $order->email,
                $order->user_id,
                $order->session_id
            ));
        }

        return redirect()->route('admin.orders.index')->with('success', 'Order updated successfully');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully');
    }

    public function drop(Order $order)
    {
        $oldStatus = $order->status;
        $order->update(['status' => 'canceled']);

        if ($oldStatus !== 'canceled') {
            $message = "Your order #{$order->id} has been canceled.";
            Notification::create([
                'user_id' => $order->user_id,
                'session_id' => $order->session_id,
                'email' => $order->email,
                'message' => $message,
                'is_read' => false,
            ]);

            event(new NotificationSent(
                $message,
                $order->email,
                $order->user_id,
                $order->session_id
            ));
        }

        return redirect()->route('admin.orders.index')->with('success', 'Order canceled successfully');
    }
}