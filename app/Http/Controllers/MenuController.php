<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends Controller
{
    /**
     * Show the welcome / order-type selection page.
     */
    public function welcome(): View
    {
        return view('customer.welcome');
    }

    /**
     * Store the chosen order type in the session.
     */
    public function setOrderType(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'order_type' => ['required', 'in:dine_in,take_away'],
        ]);

        session([
            'order_type' => $data['order_type'],
        ]);

        return redirect()->route('menu.index');
    }

    /**
     * Show the menu listing page with category filter and search.
     */
    public function index(Request $request): View|RedirectResponse
    {
        if (! session('order_type')) {
            return redirect()->route('welcome')->with('error', 'Silakan pilih tipe pesanan terlebih dahulu.');
        }

        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->with(['menuItems' => fn ($q) => $q->orderBy('sort_order')])
            ->get();

        $query = MenuItem::with('category')
            ->where('is_available', true)
            ->orderBy('sort_order');

        // Filter by category slug
        $activeCategory = $request->query('category');
        if ($activeCategory && $activeCategory !== 'semua') {
            $query->whereHas('category', fn ($q) => $q->where('slug', $activeCategory));
        }

        // Search
        $search = $request->query('search');
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $menuItems = $query->get();
        $cart = session('cart', []);
        $cartTotal = collect($cart)->sum('subtotal');

        return view('customer.menu', compact('categories', 'menuItems', 'activeCategory', 'search', 'cart', 'cartTotal'));
    }

    /**
     * Show a single menu item detail.
     */
    public function show(string $slug): View|RedirectResponse
    {
        if (! session('order_type')) {
            return redirect()->route('welcome');
        }

        $menuItem = MenuItem::where('slug', $slug)
            ->where('is_available', true)
            ->with('category')
            ->firstOrFail();

        return view('customer.menu-detail', compact('menuItem'));
    }
}
