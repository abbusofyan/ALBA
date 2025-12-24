<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RewardRedeemedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $reward;
    public $voucher;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $reward, $voucher)
    {
        $this->user = $user;
        $this->reward = $reward;
        $this->voucher = $voucher;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reward Redeemed Successfully',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reward_redeemed',
        );
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
