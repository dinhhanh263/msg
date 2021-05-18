<?php

namespace App\Http\Requests\Send;

use Illuminate\Foundation\Http\FormRequest;

class SendRequest extends FormRequest
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
			'select_template'	=> 'required',
			'text'				=> 'required',
			'csv_file'			=> 'required_if:import_method, 1|file|mimes:csv,txt|mimetypes:text/plain',
			'head_line'			=> '',
			'send_required'		=> '',
			'type'				=> 'required',
			'send_type'			=> 'required',
			'title'				=> 'required_if:send_type, 1',													// メールの場合のみ
			'import_flg'		=> 'required',
			'str_num'			=> 'required_if:send_type, 2',													// SMSの場合のみ
			'import_method'		=> 'required',																	// 1:CSV取り込み 2:1通のみ
			'customer_no'		=> 'required_if:import_method, 2|regex:/[A-Z0-9].*/|nullable',					// 1通のみの場合だけ必須

		];
	}

	public function messages()
	{
		return [
			'select_template.required'	=> '対象テンプレートを選択してください。',
			'csv_file.required_if'		=> 'csvファイルを選択してください。',
			'csv_file.mimes'			=> '拡張子がcsvのファイルを選択してください。ファイル内にHTMLタグ等が入っている場合、正常に読み込むことができません。',
			'import_flg.required'		=> '取り込み方法を選択してください。',
			'customer_no.required_if'	=> '会員番号を指定してください。',
			'customer_no.regex'			=> '正しい会員番号を入力してください。',
		];
	}

}