<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WhatsappWebhookController extends Controller
{
    //
    public function handle(Request $request)
    {
        if ($request->isMethod('get')) {
            $verify_token = 'my_verify_token';
            if ($request->hub_verify_token === $verify_token) {
                return response($request->hub_challenge, 200);
            }
            return response('Invalid verification token', 403);
        }

        // Handle incoming messages (POST)
        \Log::info('Webhook Data:', $request->all());
        return response('EVENT_RECEIVED', 200);
    }
}
