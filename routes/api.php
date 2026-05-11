<?php
use App\Http\Controllers\Api\MpesaPaymentController;


Route::post('/mpesa/initiate', [MpesaPaymentController::class, 'initiate']);
Route::post('/mpesa/stk-push', [MpesaPaymentController::class, 'stkPush']);
Route::post('/mpesa/callback', [MpesaPaymentController::class, 'callback']);
