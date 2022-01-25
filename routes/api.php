<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});


Route::group([

	'middleware' => 'api',
	'prefix' => 'auth'

], function ($router) {

	Route::post( 'register', [
		\App\Http\Controllers\AuthController::class,
		'register',
	] )->name('register');

	Route::post( 'login', [
		\App\Http\Controllers\AuthController::class,
		'login',
	] );
	Route::post( 'logout', [
		\App\Http\Controllers\AuthController::class,
		'logout',
	] );
	Route::post( 'refresh', [
		\App\Http\Controllers\AuthController::class,
		'refresh',
	] );
	Route::post( 'me', [ \App\Http\Controllers\AuthController::class, 'me' ] );

});


Route::group([
	'middleware' => 'auth:api',
	'prefix' => 'v1'
], function($router) {
	Route::post('send/invites', [\App\Http\Controllers\InviteController::class, 'send']);
	Route::post('resend/code', [\App\Http\Controllers\VerificationController::class, 'resend']);
	Route::post('code-verification', [\App\Http\Controllers\VerificationController::class, 'verify'])->name('code-verification');

	// update profile
	Route::group(['middleware' => 'user_verified'],function($router) {
		Route::post('profile/update', [\App\Http\Controllers\ProfileController::class, 'update']);
		Route::post('profile/get', [\App\Http\Controllers\ProfileController::class, 'get']);
	});
});