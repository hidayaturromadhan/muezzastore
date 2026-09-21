<?php

use App\Http\Controllers\DigiflazzWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/digiflazz/webhook', [DigiflazzWebhookController::class, 'handle']);
