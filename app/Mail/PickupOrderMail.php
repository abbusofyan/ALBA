<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PickupOrderMail extends Mailable
{
    use Queueable, SerializesModels;

	public $user;
	public $pickupOrder;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $pickupOrder)
    {
		$this->user = $user;
		$this->pickupOrder = $pickupOrder;
		$this->pickupOrder->load('wasteType');
    }

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build()
	{
		return $this->subject('Pick Up Order Submitted.')
			->view('emails.pickup_order')
			->with([
				'user' => $this->user,
				'pickupOrder' => $this->pickupOrder,
			]);
	}
}
