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
        $orders = Order::with('user')->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('user');
        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        return view('admin.orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,canceled',
            'shipping_address' => 'required|string',
        ]);

        $oldStatus = $order->status;
        $order->update($validated);

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
        $order->update(['status' => 'canceled']);

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

        return redirect()->route('admin.orders.index')->with('success', 'Order canceled successfully');
    }
}