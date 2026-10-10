<?php

use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\AppearanceController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\CouponsController;
use App\Http\Controllers\Admin\CustomersController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MarketingController;
use App\Http\Controllers\Admin\OrdersController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\ReviewsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\WebsiteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Storefront (public)
|--------------------------------------------------------------------------
| Storefront routes are added in Step 4. For now the root shows a holding
| page so the app has a valid landing route.
*/
Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Admin panel
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    // Guest (unauthenticated) admin routes.
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminLoginController::class, 'show'])->name('login');
        Route::post('login', [AdminLoginController::class, 'store'])->name('login.attempt');
    });

    // Authenticated admin routes.
    Route::middleware('auth:admin')->group(function () {
        Route::post('logout', [AdminLoginController::class, 'destroy'])->name('logout');

        Route::get('/', fn () => redirect()->route('admin.dashboard'));
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Orders — view + status updates (no public create/delete).
        Route::get('orders', [OrdersController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrdersController::class, 'show'])->name('orders.show');
        Route::put('orders/{order}', [OrdersController::class, 'update'])->name('orders.update');

        // Products — full CRUD.
        Route::put('products/{product}/feature', [ProductsController::class, 'toggleFeatured'])->name('products.feature');
        Route::resource('products', ProductsController::class)->except('show');

        // Categories — full CRUD.
        Route::resource('categories', CategoriesController::class)->except('show', 'create', 'edit');

        // Customers — CRUD (create/update/delete + detail).
        Route::resource('customers', CustomersController::class)->except('create', 'edit');

        // Blog — full CRUD.
        Route::resource('blog', BlogController::class)->parameters(['blog' => 'post'])->except('show');

        // Coupons — full CRUD.
        Route::resource('coupons', CouponsController::class)->except('show', 'create', 'edit');

        // Reviews — moderation.
        Route::get('reviews', [ReviewsController::class, 'index'])->name('reviews.index');
        Route::put('reviews/{review}', [ReviewsController::class, 'update'])->name('reviews.update');
        Route::delete('reviews/{review}', [ReviewsController::class, 'destroy'])->name('reviews.destroy');

        // Marketing — campaigns.
        Route::get('marketing', [MarketingController::class, 'index'])->name('marketing.index');
        Route::post('marketing', [MarketingController::class, 'store'])->name('marketing.store');
        Route::put('marketing/{campaign}', [MarketingController::class, 'update'])->name('marketing.update');
        Route::delete('marketing/{campaign}', [MarketingController::class, 'destroy'])->name('marketing.destroy');

        // Reports — analytics (read-only).
        Route::get('reports', [ReportsController::class, 'index'])->name('reports.index');

        // Website / Appearance / Settings — site_settings-backed (Step 5 wires storefront).
        Route::get('website', [WebsiteController::class, 'edit'])->name('website.edit');
        Route::put('website', [WebsiteController::class, 'update'])->name('website.update');

        Route::get('appearance', [AppearanceController::class, 'edit'])->name('appearance.edit');
        Route::put('appearance', [AppearanceController::class, 'update'])->name('appearance.update');

        Route::get('settings', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
    });
});
