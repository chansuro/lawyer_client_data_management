<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use MailchimpMarketing\ApiClient;
use Illuminate\Support\Facades\Log;
use App\Models\Campaign;

class SendMailchimpBulkCampaignJob implements ShouldQueue
{
    use Dispatchable,InteractsWithQueue, Queueable, SerializesModels;

    protected array $recipients;
    protected $htmlString;
    protected $dbCampaignId;

    protected $htmlTemplate = '<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
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
  <div class="email-container">
    <div class="logo">
      <img src="http://kblegalassociates.com/Dashboard/images/logo.png" alt="Company Logo">
    </div>
    <div class="content">
      [CONTENT]
    </div>
    <div class="footer">
      © [YEAR] [K & B Legal Associates]. All rights reserved.
    </div>
  </div>
</body>
</html>';

    /**
     * Create a new job instance.
     */
    public function __construct(array $recipients,string $html, string $dbCampaignId)
    {
        //
        $this->recipients = $recipients;
        $this->htmlString = $html;
        $this->dbCampaignId = $dbCampaignId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $mailchimp = new ApiClient();
        $mailchimp->setConfig([
            'apiKey' => config('services.mailchimp.key'),
            'server' => config('services.mailchimp.server_prefix'),
        ]);
        $listId = config('services.mailchimp.list_id');
        //Log::info('Using Mailchimp List ID: ' . $listId);
        // Step 1: Add each recipient with merge tags
        foreach ($this->recipients as $user) {
            try {
                $subscriberHash = md5(strtolower($user['email']));
                $mergeFields = [
                    'FNAME' => $user['first_name'],
                    'LNAME' => $user['last_name'],
                    'SUBJECT' => $user['custom_subject'],
                ];
                $mailchimp->lists->setListMember($listId,$subscriberHash,
                [
                    'email_address' => $user['email'],
                    'status_if_new' => 'subscribed',
                    'status' => 'subscribed',
                    'merge_fields' => $mergeFields,
                ]);

            } catch (\Exception $e) {
                Log::error("Mailchimp Add Error: " . $e->getMessage());
            }
        }

        // Step 2: Create campaign
        $campaign = $mailchimp->campaigns->create([
            'type' => 'regular',
            'recipients' => [
                'list_id' => $listId,
            ],
            'settings' => [
                'subject_line' => '*|SUBJECT|*', // merge tag in subject
                'title' => 'KB legal campaign',
                'from_name' => 'K & B LEGAL ASSOCIATES',
                'reply_to' => '	kblegalassociates10@gmail.com',
            ],
        ]);
        Campaign::where('id',$this->dbCampaignId)->update(['email_campaign_id'=>$campaign->id]);
        $this->htmlTemplate = str_replace('[CONTENT]',$this->htmlString,$this->htmlTemplate);
        $this->htmlTemplate = str_replace('[YEAR]',date('Y'),$this->htmlTemplate);
        // Step 3: Set campaign content using merge tags
        $mailchimp->campaigns->setContent($campaign->id, [
            'html' => $this->htmlTemplate,
        ]);

        // Step 4: Send campaign
        $mailchimp->campaigns->send($campaign->id);

    }
}
