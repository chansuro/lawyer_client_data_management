<?php

namespace App\Jobs;

use App\Services\MailchimpService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMailchimpCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $subject, $fromName, $replyTo, $htmlContent;

    public function __construct($subject, $fromName, $replyTo, $htmlContent)
    {
        $this->subject = $subject;
        $this->fromName = $fromName;
        $this->replyTo = $replyTo;
        $this->htmlContent = $htmlContent;
    }

    public function handle(MailchimpService $mailchimp)
    {
        $campaign = $mailchimp->createCampaign($this->subject, $this->fromName, $this->replyTo);

        $mailchimp->setCampaignContent($campaign->id, $this->htmlContent);
        $mailchimp->sendCampaign($campaign->id);
    }
}