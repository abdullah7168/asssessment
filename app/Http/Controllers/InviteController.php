<?php

namespace App\Http\Controllers;

use App\Http\Requests\InviteSendRequest;
use App\Models\Invite;
use App\Notifications\InviteNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Notification;

class InviteController extends Controller
{

	public function send( InviteSendRequest $request, Invite $invite) {


		$validated_data = $request->validated();

		// got the invitee email
		$invitee_email = $validated_data['email'];

		// create expireable signed url for registration
		// add an entry in invites table with user email and token
		// send email to the invitee with this link

		// create invite record
		Invite::create([
			'email' => $request->input('email')
		]);


		$register_url = URL::temporarySignedRoute(
			'register', now()->addMinutes(300)
		);

		Notification::route('mail', $invitee_email)->notify(new InviteNotification($register_url));

		return response()->json([
			'status' => 201,
			'message' => "Invites sent successfully"
		]);
    }
}
