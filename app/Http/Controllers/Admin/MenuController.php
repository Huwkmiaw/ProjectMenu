<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(Request $request): View
    {
        $query = MenuItem::with('category')->orderBy('sort_order');

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $menuItems = $query->get();
        $categories = Category::orderBy('sort_order')->get();

        return view('admin.menus.index', compact('menuItems', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.menus.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['is_available'] = $request->boolean('is_available', true);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menus', 'public');
        }

        MenuItem::create($data);

        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil ditambahkan.');
    }

    public function edit(MenuItem $menu): View
    {
        $categories = Category::orderBy('sort_order')->get();

        return view('admin.menus.edit', compact('menu', 'categories'));
    }

    public function update(Request $request, MenuItem $menu): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['is_available'] = $request->boolean('is_available');

        if ($request->hasFile('image')) {
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            $data['image'] = $request->file('image')->store('menus', 'public');
        }

        $menu->update($data);

        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(MenuItem $menu): RedirectResponse
    {
        if ($menu->image) {
            Storage::disk('public')->delete($menu->image);
        }

        $menu->delete();

        return redirect()->route('admin.menus.index')->with('success', 'Menu dihapus.');
    }

    public function toggle(MenuItem $menu): RedirectResponse
    {
        $menu->update(['is_available' => ! $menu->is_available]);

        return back()->with('success', 'Status menu diperbarui.');
    }

    /** Redirect show to edit. */
    public function show(MenuItem $menu): RedirectResponse
    {
        return redirect()->route('admin.menus.edit', $menu);
    }
}
