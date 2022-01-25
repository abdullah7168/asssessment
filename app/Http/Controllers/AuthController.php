<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use App\Models\UserVerficiationCode;
use App\Notifications\InviteNotification;
use App\Notifications\VerificationNotification;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Notification;

class AuthController extends Controller
{
	/**
	 * Create a new AuthController instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth:api', ['except' => ['login','register']]);
	}

	/**
	 * Get a JWT via given credentials.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function login()
	{
		$credentials = request(['username', 'password']);

		if (! $token = auth()->attempt($credentials)) {
			return response()->json(['error' => 'Unauthorized'], 401);
		}

		return $this->respondWithToken($token);
	}

	/**
	 * Get the authenticated User.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function me()
	{
		return response()->json(auth()->user());
	}

	/**
	 * Log the user out (Invalidate the token).
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function logout()
	{
		auth()->logout();

		return response()->json(['message' => 'Successfully logged out']);
	}

	/**
	 * Refresh a token.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function refresh()
	{
		return $this->respondWithToken(auth()->refresh());
	}

	/**
	 * Get the token array structure.
	 *
	 * @param  string $token
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function respondWithToken($token)
	{
		return response()->json([
			'access_token' => $token,
			'token_type' => 'bearer',
			'expires_in' => auth()->factory()->getTTL() * 240
		]);
	}

	/**
	 * @param \App\Http\Requests\RegistrationRequest $request
	 * @param \App\Models\User                       $user
	 *
	 * @return \Illuminate\Http\JsonResponse
	 * @throws \Exception
	 */
	public function register( RegistrationRequest $request, User $user) {

		// create new user
		$newUser = $user->create( [
			'username' => $request->input( 'username' ),
			'password' => bcrypt( $request->input( 'password' ) ),
			'email'    => $request->input( 'email' ),
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		] );

		// our new user is a customer in our system
		$newUser->assignRole(['customer']);

		(new VerificationController())->send($newUser);

		// finally once everything is set, lets get the token for the user
		// as the code verification route would auth protected
		$token = auth()->attempt(['username' => $newUser->username, 'password' => $request->input('password')]);


		return response()->json([
			'status' => 201,
			'message' => 'Your account is successfully created however in order to complete the verification process we have sent you an email, Please follow the instructions
			and complete your registration process.',
			'access_token' => [
				'access_token' => $token,
				'token_type' => 'bearer',
				'expires_in' => auth()->factory()->getTTL() * 240
			]
		]);
	}

}
