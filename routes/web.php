<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockDataController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Stock Data Routes
    Route::get('/stock-data', [StockDataController::class, 'index'])->name('stock-data.index');
    Route::get('/stock-data/upload', [StockDataController::class, 'create'])->name('stock-data.create');
    Route::post('/stock-data/upload', [StockDataController::class, 'store'])->name('stock-data.store');
    Route::get('/stock-data/{kode_saham}/charts', [StockDataController::class, 'charts'])->name('stock-data.charts');
    Route::delete('/stock-data/{id}', [StockDataController::class, 'destroy'])->name('stock-data.destroy');
    Route::delete('/stock-data', [StockDataController::class, 'deleteAll'])->name('stock-data.delete-all');
});


require __DIR__.'/auth.php';
