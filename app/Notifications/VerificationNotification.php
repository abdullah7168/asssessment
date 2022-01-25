<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerificationNotification extends Notification {

	use Queueable;

	protected $user;
	protected $code;
	protected $verification_url;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct( $user, $code, $verification_url ) {
		$this->user             = $user;
		$this->code             = $code;
		$this->verification_url = $verification_url;
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param mixed $notifiable
	 *
	 * @return array
	 */
	public function via( $notifiable ) {
		return [ 'mail' ];
	}

	/**
	 * Get the mail representation of the notification.
	 *
	 * @param mixed $notifiable
	 *
	 * @return \Illuminate\Notifications\Messages\MailMessage
	 */
	public function toMail( $notifiable ) {
		return ( new MailMessage )->greeting( "Hello!" )
		                          ->line( 'Enter this ' . $this->code . ' code by going to the given url.' . config( 'app.name' ) )
		                          ->action( 'Verification', $this->verification_url )
		                          ->line( 'Thank you!' );
	}

	/**
	 * Get the array representation of the notification.
	 *
	 * @param mixed $notifiable
	 *
	 * @return array
	 */
	public function toArray( $notifiable ) {
		return [//
		];
	}
}
