<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Create a new order from the cart (checkout).
     */
    public function store(Request $request): RedirectResponse
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang masih kosong.');
        }

        if (! session('order_type')) {
            return redirect()->route('welcome')->with('error', 'Silakan pilih tipe pesanan terlebih dahulu.');
        }

        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_note' => ['nullable', 'string', 'max:255'],
        ]);

        $orderType = session('order_type');
        $tableNumber = session('table_number');

        $orderCode = null;

        DB::transaction(function () use ($cart, $data, $orderType, $tableNumber, &$orderCode) {
            // Validate all items are still available
            $itemIds = array_keys($cart);
            $dbItems = MenuItem::whereIn('id', $itemIds)->get()->keyBy('id');

            foreach ($cart as $key => $cartItem) {
                if (! isset($dbItems[$key]) || ! $dbItems[$key]->is_available) {
                    throw new \RuntimeException("Item '{$cartItem['name']}' sudah tidak tersedia.");
                }
            }

            $total = collect($cart)->sum('subtotal');

            $order = Order::create([
                'order_code' => Order::generateOrderCode(),
                'order_type' => $orderType,
                'customer_name' => $data['customer_name'],
                'table_number' => $tableNumber,
                'customer_note' => $data['customer_note'] ?? null,
                'status' => 'pending',
                'total' => $total,
                'session_id' => session()->getId(),
            ]);

            foreach ($cart as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $cartItem['id'],
                    'menu_item_name' => $cartItem['name'],
                    'menu_item_price' => $cartItem['price'],
                    'quantity' => $cartItem['quantity'],
                    'subtotal' => $cartItem['subtotal'],
                ]);
            }

            $orderCode = $order->order_code;

            // Reset session completely so the kiosk is ready for the next customer
            session()->forget(['cart', 'order_type', 'table_number']);
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'order_code' => $orderCode,
                'message' => "Pesanan {$orderCode} berhasil dikirim ke kasir!",
                'redirect_url' => route('welcome'),
            ]);
        }

        return redirect()->route('welcome')->with('order_success', [
            'code' => $orderCode,
            'customer_name' => $data['customer_name'],
            'type' => $orderType === 'dine_in' ? "Dine In (Meja {$tableNumber})" : 'Take Away',
        ]);
    }
}
