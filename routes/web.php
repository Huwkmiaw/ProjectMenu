<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Cashier;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Customer Routes (no login required)
|--------------------------------------------------------------------------
*/

Route::get('/storage/{path}', function (string $path) {
    $filePath = storage_path('app/public/'.$path);
    if (!file_exists($filePath)) {
        abort(404);
    }
    return response()->file($filePath);
})->where('path', '.*')->name('storage.local');

Route::get('/', [MenuController::class, 'welcome'])->name('welcome');
Route::post('/order-type', [MenuController::class, 'setOrderType'])->name('order-type.set');
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::get('/menu/{slug}', [MenuController::class, 'show'])->name('menu.show');

// Cart
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::put('/{id}', [CartController::class, 'update'])->name('update');
    Route::delete('/{id}', [CartController::class, 'destroy'])->name('destroy');
    Route::delete('/', [CartController::class, 'clear'])->name('clear');
});

// Orders (customer side)
Route::post('/checkout', [OrderController::class, 'store'])->name('order.store');
Route::get('/order/{code}/confirmation', [OrderController::class, 'confirmation'])->name('order.confirmation');
Route::get('/order/{code}/tracking', [OrderController::class, 'tracking'])->name('order.tracking');

/*
|--------------------------------------------------------------------------
| Auth Routes (login/logout)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Cashier Routes
|--------------------------------------------------------------------------
*/

Route::prefix('cashier')
    ->name('cashier.')
    ->middleware(['auth', 'role:cashier,admin'])
    ->group(function () {
        Route::get('/dashboard', [Cashier\DashboardController::class, 'index'])->name('dashboard');

        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [Cashier\OrderController::class, 'index'])->name('index');
            Route::get('/pending', [Cashier\OrderController::class, 'pending'])->name('pending');
            Route::patch('/{order}/confirm', [Cashier\OrderController::class, 'confirm'])->name('confirm');
            Route::patch('/{order}/pay', [Cashier\OrderController::class, 'pay'])->name('pay');
            Route::patch('/{order}/complete', [Cashier\OrderController::class, 'complete'])->name('complete');
            Route::patch('/{order}/cancel', [Cashier\OrderController::class, 'cancel'])->name('cancel');
            Route::get('/history', [Cashier\OrderController::class, 'history'])->name('history');
        });
    });

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

        // Categories
        Route::resource('categories', Admin\CategoryController::class);
        Route::patch('categories/{category}/toggle', [Admin\CategoryController::class, 'toggle'])->name('categories.toggle');

        // Menu items
        Route::resource('menus', Admin\MenuController::class);
        Route::patch('menus/{menu}/toggle', [Admin\MenuController::class, 'toggle'])->name('menus.toggle');

        // Cashier accounts
        Route::resource('cashiers', Admin\CashierController::class);
        Route::patch('cashiers/{cashier}/toggle', [Admin\CashierController::class, 'toggle'])->name('cashiers.toggle');

        // Reports
        Route::get('/reports', [Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export-csv', [Admin\ReportController::class, 'exportCsv'])->name('reports.export-csv');
    });
