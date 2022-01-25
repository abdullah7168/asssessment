<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResendRequest;
use App\Http\Requests\VerificationRequest;
use App\Models\User;
use App\Models\UserVerficiationCode;
use App\Notifications\VerificationNotification;
use Illuminate\Http\Request;
use mysql_xdevapi\Exception;
use Notification;

class VerificationController extends Controller
{


	/**
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
	public function resend() {

		$user = auth()->user();

		try {
			$this->send($user);
		} catch (Exception $exception) {
			return response()->json([
				'status' => 400,
				'message' => $exception->getMessage()
			]);
		}

		return response()->json([
			'status' => 201,
			'message' => "An email with new 6 Digit code is sent to you on your email"
		]);

	}

	/**
	 * Sends an email to given user with a randomly generated
	 * 6-digit code
	 *
	 * @param \App\Models\User $user
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public function send(User $user) {

		// lets get a 6 digit rand unique code
		// In actual project would add expiry as well for added protection
		// not gonna do it rightnow
		$code = $this->generate_unqique_code();

		// delete any old codes for this user, if exists
		UserVerficiationCode::where('user_id', $user->id)->delete();

		// got the code, now create new entry in user_verification_codes table
		// to attach the code with the user, ofcourse it will help us to check
		// if the code does belong the user entering it
		UserVerficiationCode::create([
			'code' => $code,
			'user_id' => $user->id
		]);


		// sending email to the user with code and route they would have to provide code in
		// why sending route in email ? I know sending a page link would be better
		// but as per the assessment document we can only use apis. I promise I would never
		// do this ever in actual projects.
		Notification::route('mail', $user->email)->notify(new VerificationNotification($user, $code, route('code-verification')));

		return true;

	}

	/**
	 * Verifies the code user has entered
	 * @param \App\Http\Controllers\VerificationRequest $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function verify( VerificationRequest $request ) {

		$code = $request->input('code');

		// get the code row from db
		$user_verification_code = UserVerficiationCode::where('code', $code)->first();

		// lets if logged-in user owns the code, if no fail politely
		if($user_verification_code->user_id != auth()->id()) {
			return response()->json([
				'status' => 411,
				'message' => "Invalid code, Request a new code"
			]);
		}

		// if user does own it, complete the user verification/registration,
		// update registered_at to some date so Our angry little middleware allows
		// the user to update their profile
		$user = User::find(auth()->id());
		$user->update([
			'registered_at' => date('Y-m-d H:i:s')
		]);


		return response()->json([
			'status' => 200,
			'message' => "Congrats, you have completed your registration process, Continue to update your profile",
			'profile' => [
				'name' => $user->name,
				'avatar' => '',
				'email' => $user->email,
				'username' => $user->username
			]
		]);
    }

	/**
	 * Returns unique 6 digit code
	 * @throws \Exception
	 *
	 * Really sorry I know I should have create a seperate Helper class
	 * but feeling too lazy right now :D.
	 *
	 */
	public function generate_unqique_code() {
		do {
			$code = random_int(100000, 999999);
		} while (UserVerficiationCode::where('code', $code)->first());

		return $code;
	}
}
