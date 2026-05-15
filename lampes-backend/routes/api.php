<?php

use App\Http\Controllers\API\AdminOrderController;
use App\Http\Controllers\API\AdminUserController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CollectionController;
use App\Http\Controllers\API\ContactController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\SiteReviewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Alias publics sans version pour le catalogue
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/featured', [ProductController::class, 'featured']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/collections', [CollectionController::class, 'index']);
Route::get('/collections/{id}/products', [CollectionController::class, 'products']);
Route::post('/contact', [ContactController::class, 'store'])->middleware(['csrf.api', 'throttle:contact']);
Route::get('/reviews', [SiteReviewController::class, 'index']);
Route::post('/reviews', [SiteReviewController::class, 'store'])->middleware(['csrf.api', 'throttle:reviews']);
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword'])->middleware(['csrf.api', 'throttle:forgot-password']);
Route::post('/auth/verify-reset-code', [AuthController::class, 'verifyResetCode'])->middleware(['csrf.api', 'throttle:verify-reset-code']);
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword'])->middleware(['csrf.api', 'throttle:reset-password']);
Route::post('/payment/webhook', [PaymentController::class, 'webhook'])->middleware('throttle:payment');
Route::match(['get', 'post'], '/checkout/payment-callback', [PaymentController::class, 'callback']);
Route::post('/checkout/payment-webhook', [PaymentController::class, 'webhook'])->middleware('throttle:payment');
Route::middleware(['auth.client', 'csrf.api', 'admin'])->prefix('admin')->group(function () {
    Route::get('/orders', [AdminOrderController::class, 'orders']);
    Route::get('/payments', [AdminOrderController::class, 'payments']);
    Route::get('/contact-messages', [ContactController::class, 'index']);
    Route::get('/site-reviews', [SiteReviewController::class, 'adminIndex']);
    Route::get('/users', [AdminUserController::class, 'index']);
    Route::post('/users', [AdminUserController::class, 'store']);
    Route::put('/users/{id}', [AdminUserController::class, 'update']);
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy']);

    Route::get('/products', [ProductController::class, 'adminIndex']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
});

// Routes publiques
Route::prefix('v1')->group(function () {
    Route::get('/csrf-token', [AuthController::class, 'csrfToken']);

    // Authentification
    Route::post('/register', [AuthController::class, 'register'])->middleware(['csrf.api', 'throttle:register']);
    Route::post('/login', [AuthController::class, 'login'])->middleware(['csrf.api', 'throttle:login']);
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword'])->middleware(['csrf.api', 'throttle:forgot-password']);
    Route::post('/auth/verify-reset-code', [AuthController::class, 'verifyResetCode'])->middleware(['csrf.api', 'throttle:verify-reset-code']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword'])->middleware(['csrf.api', 'throttle:reset-password']);

    // Produits - Public
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/featured', [ProductController::class, 'featured']);
    Route::get('/products/best-sellers', [ProductController::class, 'bestSellers']);
    Route::get('/products/nouveautes', [ProductController::class, 'nouveautes']);
    Route::get('/products/category/{id}', [ProductController::class, 'byCategory']);
    Route::get('/products/{id}', [ProductController::class, 'show']);

    // Categories - Public
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/populaires', [CategoryController::class, 'populaires']);
    Route::get('/categories/{id}/products', [CategoryController::class, 'products']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);

    // Collections - Public
    Route::get('/collections', [CollectionController::class, 'index']);
    Route::get('/collections/{id}/products', [CollectionController::class, 'products']);

    // Contact - Public
    Route::post('/contact', [ContactController::class, 'store'])->middleware(['csrf.api', 'throttle:contact']);

    // Avis produit - Public
    Route::get('/products/{id_produit}/reviews', [ReviewController::class, 'index']);
});

// Routes protegees (authentification requise)
Route::prefix('v1')->middleware(['auth.client', 'csrf.api'])->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    // Panier
    Route::get('/cart', [CartController::class, 'getCart']);
    Route::get('/cart/count', [CartController::class, 'cartCount']);
    Route::post('/cart/add', [CartController::class, 'addToCart']);
    Route::put('/cart/{id_ligne}', [CartController::class, 'updateQuantity']);
    Route::delete('/cart/{id_ligne}', [CartController::class, 'removeFromCart']);
    Route::delete('/cart', [CartController::class, 'clearCart']);

    // Commandes
    Route::get('/orders/payment-config', [OrderController::class, 'paymentConfig']);
    Route::post('/orders', [OrderController::class, 'store'])->middleware('throttle:payment');
    Route::post('/orders/confirm-card-payment', [OrderController::class, 'confirmCardCheckout'])->middleware('throttle:payment');
    Route::post('/checkout/create-payment', [PaymentController::class, 'create'])->middleware('throttle:payment');
    Route::post('/checkout/payment-callback', [PaymentController::class, 'confirm'])->middleware('throttle:payment');
    Route::post('/payment/create', [PaymentController::class, 'create'])->middleware('throttle:payment');
    Route::post('/payment/confirm', [PaymentController::class, 'confirm'])->middleware('throttle:payment');
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::put('/orders/{id}/cancel', [OrderController::class, 'cancel']);
    Route::get('/contact-messages', [ContactController::class, 'myMessages']);

    // Avis produit
    Route::post('/reviews', [ReviewController::class, 'store'])->middleware('throttle:reviews');
    Route::put('/reviews/{id}', [ReviewController::class, 'update'])->middleware('throttle:reviews');
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
});

Route::middleware(['auth.client', 'csrf.api'])->group(function () {
    Route::post('/checkout/create-payment', [PaymentController::class, 'create'])->middleware('throttle:payment');
    Route::post('/checkout/payment-callback', [PaymentController::class, 'confirm'])->middleware('throttle:payment');
    Route::get('/orders', [OrderController::class, 'index']);
});

// Routes admin (authentification + admin)
Route::prefix('v1/admin')->middleware(['auth.client', 'csrf.api', 'admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/dashboard/recent-orders', [DashboardController::class, 'recentOrders']);
    Route::get('/dashboard/top-products', [DashboardController::class, 'topProducts']);
    Route::get('/dashboard/monthly-sales', [DashboardController::class, 'monthlySales']);
    Route::get('/orders', [AdminOrderController::class, 'orders']);
    Route::get('/payments', [AdminOrderController::class, 'payments']);
    Route::get('/contact-messages', [ContactController::class, 'index']);
    Route::get('/site-reviews', [SiteReviewController::class, 'adminIndex']);

    // Gestion produits
    Route::get('/products', [ProductController::class, 'adminIndex']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    // Gestion utilisateurs
    Route::get('/users', [AdminUserController::class, 'index']);
    Route::post('/users', [AdminUserController::class, 'store']);
    Route::put('/users/{id}', [AdminUserController::class, 'update']);
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy']);

    // Gestion categories
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
});
