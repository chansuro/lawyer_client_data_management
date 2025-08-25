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
        $token = config('services.whatsapp.token');
        $phoneNumberId = config('services.whatsapp.phone_number_id');
        //Log::info(['token'=>$token,'phoneNumberId'=>$phoneNumberId,'phone'=>$this->phone,'message'=>$this->message]);
        try {
            $response = Http::withToken($token)->withHeaders(['Content-Type' => 'application/json'])->post("https://graph.facebook.com/v23.0/{$phoneNumberId}/messages", [
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
        }catch (RequestException $e) {
            $status = $e->response->status();
            $body = $e->response->body();
            Log::error("HTTP error $status: $body");
        }


    }
}
