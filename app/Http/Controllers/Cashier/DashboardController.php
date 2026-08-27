<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the cashier dashboard with live pending orders.
     */
    public function index(): View
    {
        $pendingOrders = Order::pending()
            ->with('items')
            ->latest()
            ->get();

        $todayStats = [
            'pending' => Order::pending()->today()->count(),
            'confirmed' => Order::today()->where('status', 'confirmed')->count(),
            'paid' => Order::today()->where('status', 'paid')->count(),
            'completed' => Order::today()->where('status', 'completed')->count(),
            'revenue' => Order::today()->whereIn('status', ['paid', 'completed'])->sum('total'),
        ];

        return view('cashier.dashboard', compact('pendingOrders', 'todayStats'));
    }
}
