<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard with key stats.
     */
    public function index(): View
    {
        $stats = [
            'orders_today' => Order::today()->count(),
            'revenue_today' => Order::today()->whereIn('status', ['paid', 'completed'])->sum('total'),
            'orders_pending' => Order::pending()->count(),
            'total_menu_items' => MenuItem::count(),
        ];

        // Top 5 best-selling items this month
        $topMenuItems = MenuItem::withCount(['orderItems as sold_count' => fn ($q) => $q->whereHas('order', fn ($q2) => $q2->whereMonth('created_at', now()->month)
        )])
            ->orderByDesc('sold_count')
            ->limit(5)
            ->get();

        // Revenue last 7 days
        $dailyRevenue = collect(range(6, 0))->map(fn ($daysAgo) => [
            'date' => now()->subDays($daysAgo)->format('d M'),
            'revenue' => Order::whereDate('created_at', now()->subDays($daysAgo))
                ->whereIn('status', ['paid', 'completed'])
                ->sum('total'),
        ]);

        return view('admin.dashboard', compact('stats', 'topMenuItems', 'dailyRevenue'));
    }
}
