<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransferController;

Route::get('/', function () {
    return redirect()->route('accounts.index');
});

Route::resource('accounts', AccountController::class);

Route::resource('categories', CategoryController::class);

Route::resource('payment-methods', PaymentMethodController::class);

Route::resource('transactions', TransactionController::class);

Route::resource('transfers', TransferController::class);

