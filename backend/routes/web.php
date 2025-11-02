<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/test-email', function () {
    Mail::raw('Teste de email do Laravel', function ($message) {
        $message->to('samuelpbrandao58@gmail.com')
            ->subject('Teste de Email');
    });

    return 'Email enviado! Verifique sua caixa de entrada.';
});

require __DIR__ . '/settings.php';
