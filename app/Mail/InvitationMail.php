<?php

namespace App\Mail;

use App\Models\Project;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;
   public $link;
   public $ceo_name;
   public $email_template;
    /**
     * Create a new message instance.
     */
    public function __construct($link,$slug)
    {
        //
        $this->link = $link;
        $project = Project::where('slug', $slug)->first();
        $this->ceo_name = $project->client->name;
        $this->email_template = EmailTemplate::where('key', 'invitation_mail')
            ->where('business_id', $project->business_id)->first();
    }

    public function build()
    {
        $message = str_replace(['{{CEO_NAME}}', '{{INVITE_LINK}}'], [$this->ceo_name, $this->link], $this->email_template->body);
        return $this->subject($this->email_template->subject)
                    ->markdown('emails.invitation-mail', compact('message'));
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
