<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class IfUserVerified {

	/**
	 * Handle an incoming request.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
	 *
	 * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
	 */
	public function handle( Request $request, Closure $next ) {

		$user = User::find( auth()->id() );

		if ( empty( $user->registered_at ) ) {
			return response()->json( [
				"status"  => 411,
				"message" => "Your account is still not verified, Please verify your account by entering six digit code we sent you on your email or request new one",
			] );
		}

		return $next( $request );
	}
}
