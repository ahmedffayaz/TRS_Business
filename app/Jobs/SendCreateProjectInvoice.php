<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendCreateProjectInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $invoice;
    protected $fileName;
    protected $data;
    protected $emails;
    protected $filteredKeywords;
    protected $filteredKeywordsValue;

    /**
     * Create a new job instance.
     */
    public function __construct($invoice, $fileName, $data, $emails, $filteredKeywords, $filteredKeywordsValue)
    {
        $this->invoice = $invoice;
        $this->fileName = $fileName;
        $this->data = $data;
        $this->emails = $emails;
        $this->filteredKeywords = $filteredKeywords;
        $this->filteredKeywordsValue = $filteredKeywordsValue;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $emailTemplate = emailTemplate('project_invoice_creation', $this->data, $this->filteredKeywords, $this->filteredKeywordsValue);

        foreach($this->emails as $email) {
            $emailData = array(
                'name' =>  $emailTemplate['title'],
                'email' => $email,
                'emailMessage' => $emailTemplate['message'],
                'subject' => $emailTemplate['subject'],
                'businessLogo' => $this->data['business_logo']
            );

            $invoice = $this->invoice;
            $fileName = $this->fileName;
            $businessName = $this->data['business_name'];

            // Send email
            Mail::send('emails.email-template', $emailData, function ($message) use ($invoice, $fileName, $email, $businessName) {
                $message->from(env('MAIL_USERNAME'), $businessName);

                $message->to($email)->subject('Invoice creation of project');
            });
        }
    }
}
