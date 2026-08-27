<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Show sales report with filters.
     */
    public function index(Request $request): View
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $orders = Order::whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])
            ->whereIn('status', ['paid', 'completed'])
            ->with(['items', 'cashier'])
            ->latest()
            ->get();

        $summary = [
            'total_revenue' => $orders->sum('total'),
            'cash_revenue' => $orders->where('payment_method', 'cash')->sum('total'),
            'cashless_revenue' => $orders->where('payment_method', 'cashless')->sum('total'),
            'total_transactions' => $orders->count(),
            'avg_transaction' => $orders->count() > 0 ? $orders->sum('total') / $orders->count() : 0,
            'dine_in_count' => $orders->where('order_type', 'dine_in')->count(),
            'take_away_count' => $orders->where('order_type', 'take_away')->count(),
        ];

        // Best-selling items in the selected period
        $topItems = MenuItem::withCount(['orderItems as sold_count' => fn ($q) => $q->whereHas('order', fn ($q2) => $q2->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])
            ->whereIn('status', ['paid', 'completed'])
        )])
            ->orderByDesc('sold_count')
            ->get()
            ->filter(fn ($item) => $item->sold_count > 0)
            ->take(10);

        // Daily revenue for the chart
        $dailyData = $orders->groupBy(fn ($o) => $o->created_at->format('Y-m-d'))
            ->map(fn ($group) => [
                'date' => $group->first()->created_at->format('d M'),
                'revenue' => $group->sum('total'),
                'cash' => $group->where('payment_method', 'cash')->sum('total'),
                'cashless' => $group->where('payment_method', 'cashless')->sum('total'),
                'count' => $group->count(),
            ])
            ->values();

        return view('admin.reports.index', compact('orders', 'summary', 'topItems', 'dailyData', 'startDate', 'endDate'));
    }

    /**
     * Export orders as CSV download.
     */
    public function exportCsv(Request $request): Response
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $orders = Order::whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])
            ->whereIn('status', ['paid', 'completed'])
            ->with(['items', 'cashier'])
            ->latest()
            ->get();

        $csvLines = [];
        $csvLines[] = implode(',', ['Kode Order', 'Tipe', 'Nama Customer', 'Meja', 'Total', 'Metode Bayar', 'Uang Diterima', 'Kembalian', 'Status', 'Kasir', 'Tanggal']);

        foreach ($orders as $order) {
            $csvLines[] = implode(',', [
                $order->order_code,
                $order->orderTypeLabel,
                '"'.str_replace('"', '""', $order->customer_name).'"',
                $order->table_number ?? '-',
                $order->total,
                $order->paymentMethodLabel,
                $order->amount_paid ?? $order->total,
                $order->change_amount ?? 0,
                $order->statusLabel,
                $order->cashier?->name ?? '-',
                $order->created_at->format('Y-m-d H:i'),
            ]);
        }

        $filename = "laporan_{$startDate}_to_{$endDate}.csv";

        return response(implode("\n", $csvLines), 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
