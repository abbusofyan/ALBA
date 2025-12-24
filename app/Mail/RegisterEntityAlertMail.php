<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegisterEntityAlertMail extends Mailable
{
    use Queueable, SerializesModels;

	public $accountDetail;
	public $entity;

    /**
     * Create a new message instance.
     */
    public function __construct($entity, $accountDetail)
    {
		$this->entity = $entity;
		$this->accountDetail = $accountDetail;
    }

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		return $this->subject(ucfirst($this->entity) . ' Account has been created')
			->view('emails.register_entity_alert')
			->with([
				'entity' => $this->entity,
				'accountDetail' => $this->accountDetail,
			]);
	}
}
