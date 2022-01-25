<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\User;
use Illuminate\Http\Request;
use File;

class ProfileController extends Controller
{

	/**
	 * Updates user profile
	 * sets name, avatar
	 *
	 * @param \App\Http\Requests\ProfileRequest $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function update( ProfileRequest $request ) {

		// get logged-in user instance
		$user = User::find(auth()->id());

		// update the name of the user
		$user->update([
			'name' => $request->input('name')
		]);


		// check for the avatar, if it exists
		if($request->has('avatar')){

			// delete already uploaded avatars
			if(!empty($user->avatar)) {

				// search for the file,
				$avatar_old = public_path("storage/avatar/{$user->avatar->name}");

				//Check if it exists then delete it physically
				if (File::exists($avatar_old)) {
					unlink($avatar_old);
				}

				// now remove the avatar record as well from db
				$user->avatar()->delete();
			}

			// uploading the avatar
			$request->file('avatar')->store('avatars', 'public');
			$user->avatar()->create([
				'name' => $request->file('avatar')->hashName()
			]);

		}

		return response()->json([
			'status' => 201,
			'message' => 'Profile update successfully'
		]);

    }


	/**
	 * Returns logged in user's profile
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function get() {

		// get logged-in user instance
		$user = User::find(auth()->id());

		$avatar_name = $user->avatar->name ?? '';

		// only set url when avatar actually exists
		if ( ! empty( $avatar_name ) ) {
			$avatar_name = asset( 'storage/avatars/'.$avatar_name );
		}

		return response()->json([
			'name' => $user->name,
			'username' => $user->username,
			'email' => $user->email,
			'avatar' => $avatar_name
		]);

	}

}
