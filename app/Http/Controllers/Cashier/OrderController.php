<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * List all active orders (pending) for today.
     */
    public function index(): View
    {
        $orders = Order::today()
            ->where('status', 'pending')
            ->with('items')
            ->latest()
            ->get();

        return view('cashier.orders.index', compact('orders'));
    }

    /**
     * Return pending orders as JSON for live polling.
     */
    public function pending(): JsonResponse
    {
        $orders = Order::pending()
            ->with('items')
            ->latest()
            ->get()
            ->map(fn (Order $order) => [
                'id' => $order->id,
                'order_code' => $order->order_code,
                'order_type' => $order->order_type,
                'order_type_label' => $order->orderTypeLabel,
                'customer_name' => $order->customer_name,
                'table_number' => $order->table_number,
                'customer_note' => $order->customer_note,
                'total' => (float) $order->total,
                'formatted_total' => $order->formattedTotal,
                'status' => $order->status,
                'status_label' => $order->statusLabel,
                'created_at' => $order->created_at->format('H:i'),
                'items_count' => $order->items->count(),
                'items' => $order->items->map(fn ($i) => [
                    'name' => $i->menu_item_name,
                    'price' => (float) $i->menu_item_price,
                    'quantity' => $i->quantity,
                    'subtotal' => $i->formattedSubtotal,
                ]),
            ]);

        return response()->json(['orders' => $orders]);
    }

    /**
     * Process direct payment with Cash or Non-Cash (QRIS/Debit).
     */
    public function pay(Request $request, Order $order): JsonResponse|RedirectResponse
    {
        if (in_array($order->status, ['completed', 'cancelled'])) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Pesanan sudah selesai atau dibatalkan.'], 422);
            }

            return back()->with('error', 'Pesanan sudah selesai atau dibatalkan.');
        }

        $data = $request->validate([
            'payment_method' => ['required', 'in:cash,cashless'],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
        ]);

        $paymentMethod = $data['payment_method'];
        $total = (float) $order->total;
        $amountPaid = isset($data['amount_paid']) && $data['amount_paid'] !== null ? (float) $data['amount_paid'] : $total;

        if ($paymentMethod === 'cash') {
            if ($amountPaid < $total) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Nominal uang tunai kurang dari total tagihan (Rp '.number_format($total, 0, ',', '.').').',
                    ], 422);
                }

                return back()->with('error', 'Nominal uang tunai kurang dari total tagihan.');
            }
            $changeAmount = max(0, $amountPaid - $total);
        } else {
            // Non-tunai: uang pas
            $amountPaid = $total;
            $changeAmount = 0;
        }

        $order->update([
            'status' => 'completed',
            'payment_method' => $paymentMethod,
            'amount_paid' => $amountPaid,
            'change_amount' => $changeAmount,
            'cashier_id' => auth()->id(),
            'paid_at' => now(),
            'completed_at' => now(),
        ]);

        $message = "Pembayaran pesanan {$order->order_code} (".($paymentMethod === 'cash' ? 'Tunai' : 'Non-Tunai').') berhasil diselesaikan!';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'order_code' => $order->order_code,
                'payment_method' => $paymentMethod,
                'total' => $total,
                'amount_paid' => $amountPaid,
                'change_amount' => $changeAmount,
                'formatted_change' => 'Rp '.number_format($changeAmount, 0, ',', '.'),
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Cancel an order.
     */
    public function cancel(Order $order): RedirectResponse
    {
        if (in_array($order->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Pesanan tidak bisa dibatalkan.');
        }

        $order->update([
            'status' => 'cancelled',
            'cashier_id' => auth()->id(),
        ]);

        return back()->with('success', "Pesanan {$order->order_code} dibatalkan.");
    }

    /**
     * Show transaction history (supports today or any selected past date).
     */
    public function history(Request $request): View
    {
        $selectedDate = $request->get('date', now()->format('Y-m-d'));

        $orders = Order::whereDate('created_at', $selectedDate)
            ->whereIn('status', ['paid', 'completed', 'cancelled'])
            ->with(['items', 'cashier'])
            ->latest()
            ->get();

        $totalRevenue = $orders->whereIn('status', ['paid', 'completed'])->sum('total');
        $cashTotal = $orders->whereIn('status', ['paid', 'completed'])->where('payment_method', 'cash')->sum('total');
        $cashlessTotal = $orders->whereIn('status', ['paid', 'completed'])->where('payment_method', 'cashless')->sum('total');

        return view('cashier.orders.history', compact('orders', 'totalRevenue', 'cashTotal', 'cashlessTotal', 'selectedDate'));
    }
}
