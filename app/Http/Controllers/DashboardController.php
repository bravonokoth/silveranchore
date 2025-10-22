<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        // Order Analytics
        $orders = Order::where('user_id', $user->id)
            ->with(['items.product', 'shippingAddress'])
            ->latest()
            ->take(6)
            ->get();
        $totalOrders = Order::where('user_id', $user->id)->count();
        $totalSpent = Order::where('user_id', $user->id)->sum('total');
        $ordersByStatus = Order::where('user_id', $user->id)
            ->groupBy('status')
            ->selectRaw('status, count(*) as count')
            ->pluck('count', 'status')
            ->toArray();

        // Orders by day (last 7 days for bar chart)
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        $ordersByDay = Order::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupByRaw('DATE(created_at)')
            ->selectRaw('DATE(created_at) as date, count(*) as count')
            ->pluck('count', 'date')
            ->toArray();

        // Prepare labels and data for chart
        $orderLabels = [];
        $orderData = [];
        for ($date = $startDate; $date <= $endDate; $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $orderLabels[] = $date->format('M d');
            $orderData[] = $ordersByDay[$dateStr] ?? 0;
        }

        // Address Analytics
        $addresses = Address::where('user_id', $user->id)
            ->latest()
            ->take(3)
            ->get();
        $totalAddresses = Address::where('user_id', $user->id)->count();
        $shippingAddresses = Address::where('user_id', $user->id)
            ->where('type', 'shipping')
            ->count();
        $addressesByCountry = Address::where('user_id', $user->id)
            ->groupBy('country')
            ->selectRaw('country, count(*) as count')
            ->pluck('count', 'country')
            ->toArray();

        return view('dashboard', compact(
            'orders',
            'totalOrders',
            'totalSpent',
            'ordersByStatus',
            'orderLabels',
            'orderData',
            'addresses',
            'totalAddresses',
            'shippingAddresses',
            'addressesByCountry'
        ));
    }
}