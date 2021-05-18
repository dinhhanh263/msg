<?php

namespace App\Http\Requests\Upload;

use Illuminate\Foundation\Http\FormRequest;

class UploadRequest extends FormRequest
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
			'pdf_file'	=> 'required|file|mimes:pdf|max:2048',
		];
	}

	public function messages()
	{
		return [
			'pdf_file.required'	=> 'pdfファイルを選択してください。',
			'pdf_file.mimes'	=> '拡張子がpdfのファイルを選択してください。',
			'pdf_file.max'		=> 'ファイルサイズが大きすぎます。',
		];
	}
}
