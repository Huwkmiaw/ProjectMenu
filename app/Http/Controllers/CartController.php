<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Display the cart page.
     */
    public function index(): View|RedirectResponse
    {
        if (! session('order_type')) {
            return redirect()->route('welcome');
        }

        $cart = session('cart', []);
        $total = collect($cart)->sum('subtotal');

        return view('customer.cart', compact('cart', 'total'));
    }

    /**
     * Add an item to the cart (AJAX).
     */
    public function add(Request $request): JsonResponse
    {
        $data = $request->validate([
            'menu_item_id' => ['required', 'integer', 'exists:menu_items,id'],
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:99'],
        ]);

        $item = MenuItem::findOrFail($data['menu_item_id']);

        if (! $item->is_available) {
            return response()->json(['success' => false, 'message' => 'Item tidak tersedia.'], 422);
        }

        $qty = $data['quantity'] ?? 1;
        $cart = session('cart', []);
        $key = (string) $item->id;

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $qty;
            $cart[$key]['subtotal'] = $cart[$key]['price'] * $cart[$key]['quantity'];
        } else {
            $cart[$key] = [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description ?? '',
                'price' => (float) $item->price,
                'quantity' => $qty,
                'subtotal' => (float) $item->price * $qty,
                'image' => $item->image,
                'category' => $item->category->name ?? '',
            ];
        }

        session(['cart' => $cart]);

        $cartCount = collect($cart)->sum('quantity');
        $cartTotal = collect($cart)->sum('subtotal');

        return response()->json([
            'success' => true,
            'cart' => array_values($cart),
            'cart_count' => $cartCount,
            'cart_total' => $cartTotal,
            'formatted_total' => 'Rp '.number_format($cartTotal, 0, ',', '.'),
            'message' => "{$item->name} ditambahkan ke keranjang.",
        ]);
    }

    /**
     * Update the quantity of a cart item.
     */
    public function update(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $cart = session('cart', []);
        $key = (string) $id;

        if (! isset($cart[$key])) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Item tidak ditemukan.'], 404);
            }

            return back()->with('error', 'Item tidak ditemukan di keranjang.');
        }

        $cart[$key]['quantity'] = $data['quantity'];
        $cart[$key]['subtotal'] = $cart[$key]['price'] * $data['quantity'];
        session(['cart' => $cart]);

        $total = collect($cart)->sum('subtotal');
        $cartCount = collect($cart)->sum('quantity');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'cart' => array_values($cart),
                'item_subtotal' => $cart[$key]['subtotal'],
                'cart_total' => $total,
                'formatted_total' => 'Rp '.number_format($total, 0, ',', '.'),
                'cart_count' => $cartCount,
            ]);
        }

        return back();
    }

    /**
     * Remove a single item from the cart.
     */
    public function destroy(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $cart = session('cart', []);
        unset($cart[(string) $id]);
        session(['cart' => $cart]);

        $total = collect($cart)->sum('subtotal');
        $cartCount = collect($cart)->sum('quantity');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'cart' => array_values($cart),
                'cart_total' => $total,
                'formatted_total' => 'Rp '.number_format($total, 0, ',', '.'),
                'cart_count' => $cartCount,
            ]);
        }

        return back()->with('success', 'Item dihapus dari keranjang.');
    }

    /**
     * Clear the entire cart.
     */
    public function clear(): RedirectResponse
    {
        session()->forget('cart');

        return redirect()->route('menu.index')->with('success', 'Keranjang dikosongkan.');
    }
}
