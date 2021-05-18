<?php

namespace App\Library;

use App\Models\MailTemplate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use PhpParser\Node\Stmt\TryCatch;
use Exception;
use Doctrine\DBAL\Driver\AbstractDriverException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use App\Models\UpdateFileList;

class CommonFunction
{
	/**
	 * ログインチェック
	 */
	public static function checkLogin()
	{
		// ログインチェック
		$is_users = session()->has('users');

		if (!$is_users) {
			// 未ログイン
			Redirect::to('/')->send();
		}
	}

	/**
	 * api取得用
	 * @param	array		取得用配列
	 * @param	string	uri名（const.api_subの中）
	 * @return	array
	 */
	public static function getApi($param, $uri)
	{

		// 取得結果格納用
		$result = array();

		try {
			// apiのuriを設定
			$api_sub_uri = 'const.api_sub.' . $uri;

			// 取得apiのurl生成
			$url = config('const.api_url') . config($api_sub_uri);

			// ヘッダーの設定
			$header = [
				'api-token: ' . config('const.api_token'),
				'Content-Type: application/json',
				'Connection: close',
			];

			$curl = curl_init();

			curl_setopt($curl, CURLOPT_URL, $url);							// apiのURL
			curl_setopt($curl, CURLOPT_POST, true);							// post指定
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($param));	// jsonデータを送信
			curl_setopt($curl, CURLOPT_HTTPHEADER, $header);				// リクエストにヘッダーを含める
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);				// サーバ証明書の検証
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);				// 返却値を文字列で返却する
			curl_setopt($curl, CURLOPT_HEADER, true);						// ヘッダの内容
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 300);				// タイムアウトの時間

			$response = curl_exec($curl);

			$header_size	= curl_getinfo($curl, CURLINFO_HEADER_SIZE);	// 受信したヘッダのサイズ
			$body			= substr($response, $header_size);				// レスポンスを受信したヘッダのサイズにする
			$result			= json_decode($body, true);						// 取得結果をデコードする

			curl_close($curl);

			// ログ出力
			Log::channel('apilog')->info('Request Url : ' . $url . PHP_EOL . 'POST : ' . print_r(($param), true) . PHP_EOL . 'Result : ' . print_r($result, true));

			return $result;
		}
		catch (Exception $e)
		{
			// ログ出力
			Log::channel('sendsystemerrorlog')->info($e);
			return false;
		}
	}

	public static function getApiSms($param)
	{
		// 取得結果格納用
		$result = array();

		try {
			// 取得apiのurl生成
			$url = config('const.sms_url') . $param;

			// ヘッダーの設定
			$header = [
				'Accept: application/json' ,
				'token: ' . config('const.sms_token') ,
				'Content-Type: application/json',
				'Connection: close',
			];

			$curl = curl_init();

			curl_setopt($curl, CURLOPT_URL, $url);							// apiのURL
			//curl_setopt($curl, CURLOPT_POST, false);							// post指定
			//curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($param));	// jsonデータを送信
			curl_setopt($curl, CURLOPT_HTTPHEADER, $header);				// リクエストにヘッダーを含める
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);				// サーバ証明書の検証
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);				// 返却値を文字列で返却する
			curl_setopt($curl, CURLOPT_HEADER, true);						// ヘッダの内容
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 300);				// タイムアウトの時間

			$response = curl_exec($curl);

			$header_size	= curl_getinfo($curl, CURLINFO_HEADER_SIZE);	// 受信したヘッダのサイズ
			$body			= substr($response, $header_size);				// レスポンスを受信したヘッダのサイズにする
			$result			= json_decode($body, true);						// 取得結果をデコードする


			curl_close($curl);

			// ログ出力
			Log::channel('apilog')->info('Request Url : ' . $url . PHP_EOL . 'Result : ' . print_r($result, true));

			return $result;
		}
		catch (Exception $e)
		{
			// ログ出力
			Log::channel('sendsystemerrorlog')->info($e);
			return false;
		}
	}

	/**
	 * apiエラーコード日本語化
	 *
	 * @param	array	$ary_param	api取得結果
	 * @param	string	$sCode		api取得結果のエラ〜コードが入っているカラム名
	 *
	 */
	public static function apiErrorCodeJapaneseCsv($ary_param, $head_line, $sCode) {
		$ary_edit = array();

		// bodyのみにする
		$ary_param = (array)$ary_param['body'];

		$i = 0;

		foreach ($ary_param as $line_num => $ary_value)
		{
			// キー名に+1をする（行数）
			if (!$head_line)
			{
				// 先頭行を含めない場合
				$key_line_num = $line_num + 2;
			}
			else
			{
				// 先頭行を含める場合
				$key_line_num = $line_num + 1;
			}

			$ary_edit[$i]['num'] = $key_line_num;

			foreach ($ary_value as $key_name => $ary_item)
			{
				if ($key_name == 'faliReason')
				{
					$ary_edit[$i][$key_name] = config('const.api_error_code.' . $ary_item);
				}
				else
				{
					$ary_edit[$i][$key_name] = $ary_item;
				}
			}

			$i++;
		}
		return $ary_edit;
	}

	/**
	 * smsバリデーションエラー項目日本語化
	 */
	public static function smsErrorValidationErrorJapanese($param)
	{
		// 編集後の格納対象
		$ary_edit_data = array();

		foreach ($param as $ary_val)
		{
			$ary_edit_data[] = [
				'failCustomerKey'	=> $ary_val['failCustomerKey'],
				'faliReason'		=> config('const.api_error_code.' . $ary_val['faliReason']),
			];
		}

		return $ary_edit_data;
	}

	/**
	 * テンプレート一覧
	 */
	public static function getTemplateList($send_type = '')
	{
		// ユーザーデータをセッションから取得
		$ary_user_data = session()->get('users');

		// クエリー使用
		$query = MailTemplate::query();

		// 削除フラグ＝0を指定
		$query->where('del_flg', 0);

		// 検索するsend_typeが指定されている場合
		if (!empty($send_type))
		{
			$query->where('send_type', $send_type);
		}
		else
		{
			// 送信種別が指定されていない場合は全てを表示するが、タイプの制限がある為かけておく
			$query->whereIn('send_type', array_keys(config('const.send_type')));
		}

		// ADMINでログインしている場合はどの部署の結果も見れるようにする
		if ($ary_user_data['groupCd'] != config('const.department_code.DEP_CODE_ADMIN'))
		{
			$query->where('group_cd', $ary_user_data['groupCd']);
		}

		return $query->get()->toArray();
	}

	/**
	 * 置き換え文字チェック
	 * @text		string	$text
	 * @send_type	integer	$send_type
	 * @return string|NULL
	 */
	public static function checkReplaceString($text, $send_type)
	{
		// 差し込み文字を取得
		if ($send_type == config('const.SEND_TYPE_MAIL'))
		{
			// メール
			preg_match_all('/{{insertion_item([1-9]|10)}}/', $text, $matchs);
		}
		else if ($send_type == config('const.SEND_TYPE_SMS'))
		{
			// SMS
			preg_match_all('/{{insertion_item[1-5]}}/', $text, $matchs);
		}

		// 一旦差し込み文字列を除外する
		$tmp_text = $text;
		if (!empty($matchs[0]))
		{
			foreach ($matchs as $value)
			{
				$tmp_text = str_replace($value, '', $tmp_text);
			}
		}

		// 差し込み不可な文字があるかチェック
		preg_match_all('/{{.*}}/', $tmp_text, $matchs_error);

		// 全角で囲まれた{{}}があるかチェック
		preg_match_all('/｛｛.*｝｝/', $tmp_text, $matchs_error2);

		if(!empty($matchs_error[0]) || !empty($matchs_error2[0]))
		{
			return '差し込み不可な文字列が存在します。';
		}

		// 差し込み文字列が上限を超えていた場合
		if(count($matchs[0]) > 10)
		{
			return '差し込みをする文字列が多すぎます。';
		}

		return null;
	}

	/**
	 * アップロードしたリストを取得
	 */
	public static function urlList($limit = '')
	{
		// 現在のログイン情報を取得
		$ary_user_data = session()->get('users');

		// クエリー使用
		$query = UpdateFileList::query();

		// 削除フラグ＝0を指定
		$query->where('del_flg', 0);

		// ADMINでログインしている場合はどの部署の結果も見れるようにする
		if ($ary_user_data['groupCd'] != config('const.department_code.DEP_CODE_ADMIN'))
		{
			$query->where('group_cd', $ary_user_data['groupCd']);
		}

		// 件数指定がある場合
		if (!empty($limit))
		{
			$query->limit($limit);
		}

		return $query->orderBy('edit_date', 'desc')->get()->toArray();
	}
}