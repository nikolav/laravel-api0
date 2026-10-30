<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MessagePlainEmail extends Mailable implements ShouldQueue
{
  use Queueable, SerializesModels;

  /**
   * Create a new message instance.
   */
  function __construct(
    public $view    = 'emails.message-plain',
    public $subject = 'Plain email message',
    public $data    = []
  ) {}

  /**
   * Get the message envelope.
   */
  function envelope(): Envelope
  {
    return new Envelope(
      subject: $this->subject,
    );
  }

  /**
   * Get the message content definition.
   */
  function content(): Content
  {
    return new Content(
      view: $this->view,
      with: ['data' => $this->data],
    );
  }

  /**
   * Get the attachments for the message.
   *
   * @return array<int, \Illuminate\Mail\Mailables\Attachment>
   */
  function attachments(): array
  {
    return [];
  }
}
