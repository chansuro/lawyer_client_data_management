<?php

namespace App\Jobs;

use App\Mail\MailgunMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Customer;

class SendEmailMailgun implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected array $recipients;
    protected $htmlString;
    protected $campaignId;
    /**
     * Create a new job instance.
     */
    public function __construct(array $recipients, string $campaignId)
    {
        //
        $this->recipients = $recipients;
        //$this->htmlString = $html;
        $this->campaignId = $campaignId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->recipients as $user) {
            try {
                    // $details = [
                    //     'year'=>date('Y'),
                    //     'body' =>$this->htmlString,
                    //     'custid'=>$user['id']
                    // ];
                    //Mail::to($user['email'])->send(new MailgunMail($details, $user['custom_subject']));
                    // Extract message ID from the Mailgun headers
                    //$messageId = optional(Mail::getSwiftMailer()->getTransport()->getLastResponse())['id'] ?? null;

                    //Log::info('Mailgun Message ID: ' . $messageId);
                    $html = "<!DOCTYPE html>
                    <html>
                    <head>
                      <meta charset=\"UTF-8\">
                      <title>Email Template</title>
                      <style>
                        body {
                          font-family: Arial, sans-serif;
                          background-color: #f4f4f4;
                          margin: 0;
                          padding: 0;
                        }
                        .email-container {
                          background-color: #ffffff;
                          max-width: 600px;
                          margin: 40px auto;
                          padding: 20px;
                          border-radius: 10px;
                          box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                        }
                        .logo {
                          text-align: center;
                          margin-bottom: 20px;
                        }
                        .logo img {
                          max-width: 150px;
                          height: auto;
                        }
                        .content {
                          font-size: 16px;
                          color: #333333;
                          line-height: 1.6;
                        }
                        .footer {
                          text-align: center;
                          margin-top: 30px;
                          font-size: 12px;
                          color: #999999;
                        }
                      </style>
                    </head>
                    <body>
                      <div class=\"email-container\">
                        <div class=\"logo\">
                          <img src=\"http://kblegalassociates.com/Dashboard/images/logo.png\" alt=\"Company Logo\">
                        </div>
                        <div class=\"content\">
                        ".$user['custom_message']."
                        </div>
                        <div class=\"footer\">
                          © ".date('Y')." [".config('services.mailgun.from_name')."]. All rights reserved.
                        </div>
                      </div>
                    </body>
                    </html>";
                    $response = Http::withBasicAuth('api', config('services.mailgun.secret'))
                        ->asForm() // This is important!
                        ->post("https://api.mailgun.net/v3/".config('services.mailgun.domain')."/messages", [
                            'from' => config('services.mailgun.from_name').' <'.config('services.mailgun.from_email').'>',  // ✅ Must be verified in Mailgun
                            'to' => $user['email'],
                            'subject' => $user['custom_subject'],
                            'html' => $html,
                        ]);

                    if ($response->successful()) {
                        $messageId = $response->json('id'); // e.g. '<20250619094020.1D3456789C@example.com>'
                        Customer::where('id', $user['id'])->update(['message_id'=>$messageId]);
                        \Log::info("Mailgun Message ID: $messageId");
                    } else {
                        \Log::error("Mailgun send failed: " . $response->body());
                    }

                //$messageId = $response->json('id');
                Log::info("Mailgun Message ID: ".$messageId);
            } catch (\Exception $e) {
                Log::error("Mailgun Add Error: " . $e->getMessage());
            }
        }
    }
}
