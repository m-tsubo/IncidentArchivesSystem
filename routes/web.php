<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IncidentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware('auth')->group(function () {
    Route::get('/get-incidents-by-year-month/{year}/{month}', [DashboardController::class, 'getIncidentsByYearMonth']);
    Route::get('/dashboard/get-by-search', [DashboardController::class, 'getBySearch']);

    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 明示的に定義されている incident のルート
    Route::get('/incident/create', [IncidentController::class, 'create'])->name('incident.create');
    Route::post('/incident/create', [IncidentController::class, 'store'])->name('incident.store');
    Route::get('/get-all-incidents', [IncidentController::class, 'getAllIncidents']);
    
    // 上記で明示的に定義しているルートを除外したリソースルート
    Route::resource('incident', IncidentController::class)->except(['create', 'store']);

});

require __DIR__.'/auth.php';
