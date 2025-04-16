<?php
namespace App\Services;

use MailchimpMarketing\ApiClient;

class MailchimpService
{
    protected $client;

    public function __construct()
    {
        $this->client = new ApiClient();
        $this->client->setConfig([
            'apiKey' => config('services.mailchimp.key'),
            'server' => config('services.mailchimp.server_prefix'),
        ]);
    }

    public function addSubscriber(string $email, string $firstName = '', string $lastName = '')
    {
        return $this->client->lists->addListMember(config('services.mailchimp.audience_id'), [
            'email_address' => $email,
            'status' => 'subscribed',
            'merge_fields' => [
                'FNAME' => $firstName,
                'LNAME' => $lastName,
            ],
        ]);
    }

    public function createCampaign(string $subject, string $fromName, string $replyTo)
    {
        return $this->client->campaigns->create([
            'type' => 'regular',
            'recipients' => [
                'list_id' => config('services.mailchimp.audience_id'),
            ],
            'settings' => [
                'subject_line' => $subject,
                'from_name' => $fromName,
                'reply_to' => $replyTo,
            ],
        ]);
        // $response = $this->client->post('campaigns', [
        //     'json' => [
        //         'type' => 'regular',
        //         'recipients' => [
        //             'list_id' => config('services.mailchimp.audience_id'),
        //         ],
        //         'settings' => [
        //             'subject_line' => $subject,
        //             'reply_to' => $replyTo,
        //             'from_name' => $fromName,
        //         ],
        //     ],
        // ]);
        // return json_decode((string) $response->getBody(), true);
    }

    public function setCampaignContent(string $campaignId, string $htmlContent)
    {
        return $this->client->campaigns->setContent($campaignId, [
            'html' => $htmlContent,
        ]);
    }

    public function sendCampaign(string $campaignId)
    {
        return $this->client->campaigns->send($campaignId);
    }
}