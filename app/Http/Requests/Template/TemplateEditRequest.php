<?php

namespace App\Http\Requests\Template;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class TemplateEditRequest extends FormRequest
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
			'template_name' => 'required|space|max:50',
			'text'			=> 'required|space',
			'url_input'		=> '',
			'type'			=> 'required',
			'send_type'		=> 'required',
			'confirm_flg'	=> '',
			'title'			=> 'required_if:send_type, 1',
			'url_type'		=> '',
			'url_choice'	=> '',
			'url'			=> 'nullable|regex:/^[!-~]+$/',
		];
	}

	public function messages()
	{
		return [
			'template_name.space'		=> 'スペースのみは登録できません。',
			'template_name.required' 	=> 'テンプレート名を入力してください。',
			'template_name.max'			=> '50文字以内で入力してください。',
			'text.space'				=> 'スペースのみは登録できません。',
			'text.required'				=> '本文を入力してください。',
			'type.required'				=> '配信元事業部を選択してください。',
			'send_type.required'		=> '配信種別を選択してください。',
			'url.regex'					=> '半角英数記号で入力してください。',
			'title.required_if'			=> 'メールを選択した場合は件名を指定してください。',
		];
	}
}
