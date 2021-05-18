<?php

namespace App\Library;

use \SplFileObject;

class CommonCsv
{
	/**
	 * csv読み込み
	 *
	 * @param array $validated
	 */
	public function csvFileCheck($validated)
	{
		// ファイルを格納
		$tmp_csv_file = $validated['csv_file'];

		// 先頭行使用するか
		$head_line_flg = isset($validated['head_line']) ? true : false;

		// ファイルを取り出す
		$file = new SplFileObject($tmp_csv_file);
		$file->setFlags(
			\SplFileObject::READ_CSV|		// csvとして読み込む
			\SplFileObject::SKIP_EMPTY|		// 空行は読み飛ばす
			\SplFileObject::READ_AHEAD		// 先読み/巻き戻しで読み出す
			);

		// バリデーションチェック（空行チェックなど）
		$ary_validated_csv = $this->validationCheckCsv($file, $head_line_flg, $validated['send_type']);

		return $ary_validated_csv;
	}

	/**
	 * CSVのバリデーションチェック
	 * @param	string $param
	 * @return	array	$ary_check_result
	 */
	public function validationCheckCsv($ary_file, $head_line_flg, $send_type)
	{
		$i				= 1;		// ループカウント
		$ary_error		= array();	// エラー配列
		$ary_success	= array();	// バリデーションOK配列

		foreach ($ary_file as $value)
		{
			// １行目のみBOM除外処理
			if ($i == 1)
			{
				$value = preg_replace('/^\xEF\xBB\xBF/', '', $value);
			}

			// 件数チェック(SMSのみ)
			if ($send_type == config('const.SEND_TYPE_SMS'))
			{
				if (($head_line_flg && $i > config('const.sms_send_max_num.' . $send_type)) ||		// ヘッダー含む
					(!$head_line_flg && $i > (config('const.sms_send_max_num.' . $send_type) + 1)))	// ヘッダー含まない
				{
					$ary_data['msg'] = 'CSVの件数が1万件以上あります。1万件以上送信する場合はシステム部へご依頼ください。';
					return $ary_data;
				}
			}

			// 先頭行を含めない場合
			if (!$head_line_flg && $i == 1) {
				$i++;
				continue;
			}

			// 1列目に会員番号または電話番号が入っていると想定
			if ($this->validationCheckCsvRecode($value[0]))
			{
				// 空行の場合、行数を配列に入れる
				$ary_error[] = $i;
			}
			else
			{
				$ary_success[$i] = $value;
			}
			$i++;
		}

		$ary_data = array();

		if (count($ary_error) == 0 && count($ary_success) == 0)
		{
			$ary_data['msg'] = 'CSVの件数が0件です。ファイルを確認してください。';
		}
		else
		{
			$ary_data['ERROR'] = $ary_error;
			$ary_data['SUCCESS'] = $ary_success;
		}

		return $ary_data;
	}

	/**
	 * １レコードずつのバリデーションチェック（会員IDまたは電話番号
	 * @param string $param
	 * @return boolean	true : 空行, false : 空行以外
	 */
	public function validationCheckCsvRecode($param)
	{
		// 空白除去
		$param = str_replace(' ', '', $param);	// 半角スペース除去
		$param = str_replace('　', '', $param);	// 全角スペース除去

		// 空チェック
		return empty($param) ? true : false;
	}

	public function putErrorCsv($ary_error_data, $request_id)
	{
		// 出力ファイル名
		$file_name = storage_path('send_log/' . $request_id . '.csv');
		mb_convert_variables('SJIS-win', 'UTF-8', $file_name); //文字化け対策

		// 先頭行
		$head_line = [
			'error_type',
			'error_code',
			'csv_str',
		];

		mb_convert_variables('SJIS-win', 'UTF-8', $head_line); //文字化け対策

		$createCsvFile = fopen($file_name, 'a');

		fputcsv($createCsvFile, $head_line); //ファイルに追記する

		foreach ($ary_error_data['item'] as $ary_value)
		{
			$csv = array();
			// １列目のみ（error_type:display, api）
			$csv[] = 'display';
			foreach ($ary_value as $ary_value2)
			{
				$csv[] = $ary_value2;
			}

			mb_convert_variables('SJIS-win', 'UTF-8', $csv); //文字化け対策

			fputcsv($createCsvFile, $csv); //ファイルに追記する
		}
		fclose($createCsvFile); //ファイル閉じる
	}

	public static function putErrorCsvTmp($ary_error_data, $str_code_column)
	{
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=validation_error.csv');

		$csv = fopen('php://output', "w");

		if ($csv)
		{
			// BOM対策
			stream_filter_prepend($csv, 'convert.iconv.utf-8/utf-8');
			fwrite($csv, "\xEF\xBB\xBF");

			// 1行目
			$head_line =
				[
					'対象行',
					'エラー箇所',
					'エラー内容'
				];

			fputcsv($csv, $head_line);

			foreach ($ary_error_data as $key => $value)
			{
				$ary_put_data = array();

				foreach ($value as $str)
				{
					$ary_put_data[] = $str;
				}

				fputcsv($csv, $ary_put_data);
			}
		}

		fclose($csv);

	}

	public static function putErrorSms($param)
	{
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=smsvalidation_error.csv');

		$csv = fopen('php://output', "w");

		if ($csv)
		{
			// BOM対策
			stream_filter_prepend($csv, 'convert.iconv.utf-8/utf-8');
			fwrite($csv, "\xEF\xBB\xBF");

			// 1行目
			$head_line =
			[
				'エラー項目',
				'エラー内容'
			];

			fputcsv($csv, $head_line);

			foreach ($param as $key => $value)
			{
				$ary_put_data = array();

				foreach ($value as $str)
				{
					$ary_put_data[] = $str;
				}

				fputcsv($csv, $ary_put_data);
			}
		}
		fclose($csv);
	}
}