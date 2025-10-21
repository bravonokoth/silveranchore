<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::post('/paystack/webhook', [PaymentController::class, 'handleWebhook']);
