<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
			// Please pardon my spelling mistakes, i know its bad
	        // but i am writing code at 240 m/h
            'code' => 'required|exists:user_verficiation_codes'
        ];
    }

	/**
	 * @return string[]
	 */
	public function messages() {
		return [
			'code.required' => 'Verification code cannot be empty',
			'code.exists' => 'Invalid Verification code'
		];
	}
}
