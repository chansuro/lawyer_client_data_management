<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MailgunWebhookController;
use App\Http\Controllers\WhatsappWebhookController;

Route::post('/mailgun/webhook', [MailgunWebhookController::class, 'handle']);
Route::match(['GET', 'POST'], '/whatsapp/webhook', [WhatsappWebhookController::class, 'handle']);
Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});