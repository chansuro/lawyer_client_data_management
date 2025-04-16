<?php

namespace App\Jobs;

use App\Services\MailchimpService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddMailchimpSubscriber implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $email, $firstName, $lastName;

    public function __construct($email, $firstName = '', $lastName = '')
    {
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function handle(MailchimpService $mailchimp)
    {
        $mailchimp->addSubscriber($this->email, $this->firstName, $this->lastName);
    }
}