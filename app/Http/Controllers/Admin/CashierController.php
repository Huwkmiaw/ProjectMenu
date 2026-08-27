<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CashierController extends Controller
{
    public function index(): View
    {
        $cashiers = User::where('role', 'cashier')->latest()->get();

        return view('admin.cashiers.index', compact('cashiers'));
    }

    public function create(): View
    {
        return view('admin.cashiers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'role' => 'cashier',
            'is_active' => true,
        ]);

        return redirect()->route('admin.cashiers.index')->with('success', 'Akun kasir berhasil dibuat.');
    }

    public function edit(User $cashier): View
    {
        abort_if($cashier->role === 'admin', 403);

        return view('admin.cashiers.edit', compact('cashier'));
    }

    public function update(Request $request, User $cashier): RedirectResponse
    {
        abort_if($cashier->role === 'admin', 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($cashier->id)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $updateData = [
            'name' => $data['name'],
            'username' => $data['username'],
        ];

        if (! empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $cashier->update($updateData);

        return redirect()->route('admin.cashiers.index')->with('success', 'Akun kasir diperbarui.');
    }

    public function destroy(User $cashier): RedirectResponse
    {
        abort_if($cashier->role === 'admin', 403);

        $cashier->delete();

        return redirect()->route('admin.cashiers.index')->with('success', 'Akun kasir dihapus.');
    }

    public function toggle(User $cashier): RedirectResponse
    {
        abort_if($cashier->role === 'admin', 403);

        $cashier->update(['is_active' => ! $cashier->is_active]);

        return back()->with('success', 'Status kasir diperbarui.');
    }

    /** Redirect show to edit. */
    public function show(User $cashier): RedirectResponse
    {
        return redirect()->route('admin.cashiers.edit', $cashier);
    }
}
