<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Customer;

class MailgunWebhookController extends Controller
{
    //
    public function handle(Request $request)
    {
        $eventData = $request->all();
        $filterdata = [];
        if($eventData['event-data']['event'] == 'delivered'){
            $filterdata['recipient'] = $eventData['event-data']['recipient'] ?? null;
            $filterdata['messageId'] = '<'.$eventData['event-data']['message']['headers']['message-id'].'>' ?? null;
            
         Customer::where('email', $filterdata['recipient'])->where('message_id', $filterdata['messageId'])->update(['email_status'=>'Delivered']); 
            // Log or save to DB
        Log::info('Mailgun webhook:', $filterdata);
        }
        

        // Optional: validate signature
        // $request->validate([...]);

        return response()->json(['status' => 'ok'], 200);
    }
}
