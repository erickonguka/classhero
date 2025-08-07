<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/set-currency', function(Request $request) {
    $currency = $request->input('currency', 'USD');
    session(['guest_currency' => $currency]);
    return response()->json(['success' => true]);
})->name('set.currency');