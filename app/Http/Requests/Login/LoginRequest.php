<?php

namespace App\Http\Requests\Login;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
			'login_id'	=> 'required|regex:/^[a-zA-Z0-9]+$/',
			'password'	=> 'required|regex:/^[a-zA-Z0-9]+$/',
		];
	}

	public function messages()
	{
		return [
			'required' => '必須項目です。',
			'regex' => '半角英数字で入力してください。',
		];
	}
}
