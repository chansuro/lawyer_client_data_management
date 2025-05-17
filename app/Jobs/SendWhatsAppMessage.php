<?php

namespace App\Jobs;

//use App\Services\TwilioWhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class SendWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $phone;
    public $message;
    public $variables = [];

    public function __construct($phone, $message,$variableArr)
    {
        $this->phone = $phone;
        $this->message = $message;
        $this->variables = $variableArr;
    }

    public function handle()
    {
        // for twilio whatsapp message sending
        //$twilio->sendMessage($this->phone, $this->message);

        $token = config('services.whatsapp.token');
        $phoneNumberId = config('services.whatsapp.phone_number_id');
        //Log::info(['token'=>$token,'phoneNumberId'=>$phoneNumberId,'phone'=>$this->phone,'message'=>$this->message]);
        try {
            // $response = Http::withToken($token)->post("https://graph.facebook.com/v18.0/{$phoneNumberId}/messages", [
            //     'messaging_product' => 'whatsapp',
            //     'to' => $this->phone,
            //     'type' => 'text',
            //     'text' => [
            //         'body' => $this->message
            //     ],
            // ]);

            $response = Http::withToken($token)->post("https://graph.facebook.com/v18.0/{$phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $this->phone,
                'type' => 'template',
                'template' => [
                    'name'=>'template_1',
                    'language' => ['code' => 'en'],
                    'components'=>[
                        [
                            'type' => 'body',
                            'parameters' => $this->variables,
                        ],
                    ],
                ],
            ]);
            $data = $response->json();
            Log::info($data);
        }catch (RequestException $e) {
            // Handle the HTTP error
            $status = $e->response->status();
            $body = $e->response->body();
            Log::error("HTTP error $status: $body");
        }


    }
}
