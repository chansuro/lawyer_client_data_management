<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use MailchimpMarketing\ApiClient;
use Illuminate\Support\Facades\Log;

class SendMailchimpBulkCampaignJob implements ShouldQueue
{
    use Dispatchable,InteractsWithQueue, Queueable, SerializesModels;

    protected array $recipients;
    protected $htmlString;

    /**
     * Create a new job instance.
     */
    public function __construct(array $recipients,string $html)
    {
        //
        $this->recipients = $recipients;
        $this->htmlString = $html;
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
                // $mailchimp->lists->addListMember($listId, [
                //     'email_address' => $user['email'],
                //     'status' => 'subscribed',
                //     'merge_fields' => [
                //         'FNAME' => $user['first_name'],
                //         'LNAME' => $user['last_name'],
                //         'CUSTSUBJ' => $user['custom_subject'],
                //     ],
                // ]);

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
        Log::info('Created campaign ID:', ['id' => $campaign->id]);
        // Step 3: Set campaign content using merge tags
        $mailchimp->campaigns->setContent($campaign->id, [
            'html' => $this->htmlString,
        ]);

        // Step 4: Send campaign
        $mailchimp->campaigns->send($campaign->id);

    }
}
