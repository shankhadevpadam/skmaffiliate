<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    // return Inertia::render('Welcome');
    return Inertia::render('auth/Login', [
        'canResetPassword' => Route::has('password.request'),
        'status' => request()->session()->get('status'),
    ]);
})
    ->middleware('guest')
    ->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('subscribers', App\Http\Controllers\Affiliate\SubscribersController::class);
    Route::resource('campaigns', App\Http\Controllers\Affiliate\CampaignsController::class);
    Route::resource('templates', App\Http\Controllers\Affiliate\TemplatesController::class);
    Route::get('templates/{template}/create-campaign', [App\Http\Controllers\Affiliate\TemplatesController::class, 'createCampaign'])->name('templates.create-campaign');
    Route::post('templates/{template}/store-campaign', [App\Http\Controllers\Affiliate\TemplatesController::class, 'storeCampaign'])->name('templates.store-campaign');

    Route::get('/preview/{record}', function (App\Models\Template $record) {
        return response($record->content)
            ->header('Content-Type', 'text/html');
    })->name('html.preview');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
