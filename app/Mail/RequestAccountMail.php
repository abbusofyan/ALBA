<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RequestAccountMail extends Mailable
{
    use Queueable, SerializesModels;

	public $accountDetail;

    /**
     * Create a new message instance.
     */
    public function __construct($accountDetail)
    {
		$this->accountDetail = $accountDetail;
    }

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		return $this->subject('Request for ' . ucfirst($this->accountDetail['type']) . ' Account')
			->view('emails.request_account')
			->with([
				'accountDetail' => $this->accountDetail,
			]);
	}
}
