<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Attachment;


class NotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;
    protected $backgroundImage;
    public $subject;
    public $view;

    /**
     * Create a new message instance.
     * @param array $data - data to be sent in the email body
     * @param bool $isBcc - flag to send a copy of the email to the BCC email
     * @return void
     */
    public function __construct(array $data, string $subject, string $view)
    {
        $this->data = $data;
        $this->view = $view;
        $this->subject = $subject;
        $this->backgroundImage = public_path('/front/images/mailBgColor.png');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('GTSystem@tersys.fr', 'GTSystem@notification'),
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: $this->view,
            with: [
                'data' => $this->data,
                'backgroundImage' => $this->backgroundImage,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath(public_path('/front/images/logo.png'))->as('logo.png')->withMime('image/png'),
        ];
    }
}
