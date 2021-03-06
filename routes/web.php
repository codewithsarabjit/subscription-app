<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\SubscriptionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [SubscriptionController::class, 'dashboard'])->name('dashboard');
    Route::name('plans.')->group(function () {
        Route::get('/plans', [SubscriptionController::class, 'index'])->name('index');
        Route::put('/plans', [SubscriptionController::class, 'update'])->name('update');
        Route::get('/pay/{prodid}', [SubscriptionController::class, 'pay'])->name('pay');
    });
    Route::post('/products/{id}/purchase', [SubscriptionController::class, 'purchase'])->name('products.purchase');
});
