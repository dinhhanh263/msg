<?php

namespace App\Http\Controllers\Send;

use App\Http\Controllers\Controller;
use App\Http\Requests\Send\SendRequest;
use App\Models\MailTemplate;
use App\Models\kireimo\customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Library\CommonCsv;
use App\Library\CommonFunction;

class SendController extends Controller
{

	public function __construct()
	{
		// ログインチェック
		CommonFunction::checkLogin();

		// 共通変数定義
		$this->template_list = CommonFunction::getTemplateList(); // テンプレート取得
	}

	public function indexId($id)
	{
		// ユーザー情報を取得する
		$ary_user_data = session()->get('users');

		// 編集対象のレコードを取得
		$ary_template = MailTemplate::where('id', $id)->where('del_flg', 0)->get()->toArray();

		// 一覧へリダイレクト処理
		// 条件1:取得結果が空
		// 条件2:ADMINユーザー以外
		// 条件3:グループコードが一致しない
		if (empty($ary_template) ||
			($ary_user_data['login_id'] != config('const.department_code.DEP_CODE_ADMIN') && $ary_user_data['groupCd'] != $ary_template[0]['group_cd']))
		{
			// 存在しない場合は一覧へ
			return redirect()->action('Template\ListController@index');
		}

		// 文字数カウント（SMSのみ）
		if ($ary_template[0]['send_type'] == config('const.SEND_TYPE_SMS'))
		{
			$tmp_text = $ary_template[0]['text'];

			// 置き換え文字を除外
			for ($i = 1; $i <= config('const.send_insert_max_num.' . $ary_template[0]['send_type']); $i++)
			{
				$tmp_text = str_replace('{{insertion_item' . $i . '}}', '', $tmp_text);
			}

			// 文字数カウント
			$ary_template[0]['str_num'] = mb_strlen($tmp_text);

			// URLが存在する場合
			if (!empty($ary_template[0]['url']))
			{
				$ary_template[0]['str_num'] = $ary_template[0]['str_num'] + 23;

				$ary_template[0]['text'] = $ary_template[0]['text'] . ' ' . $ary_template[0]['url'];
			}
		}

		// 送信画面へ遷移する
		return view('send.index_id',
			[
				'ary_template'	=> $ary_template,
			]);
	}

	public function confirm(SendRequest $request)
	{
		// セッションに保持しているエラーを削除
		session()->forget('send_error');
		session()->forget('error_msg');
		session()->forget('ary_error_data');

		// バリデーション完了後の値を取り出す
		$validated = $request->validated();

		$commonCsv = new CommonCsv();

		// csvファイルチェック
		if ($validated['import_method'] == config('const.IMPORT_METHOD_CSV'))
		{
			// CSVから取り込む場合
			$ary_check_result = $commonCsv->csvFileCheck($validated);
		}

		// 強制送信するか
		$send_required_flg = isset($validated['send_required']) ? true : false;

		// 先頭行含めるか
		$head_line_flg = empty($validated['head_line']) ? false : true;
		session()->put('head_line_flg', $head_line_flg);

		// 確認画面にデータ送る配列
		$ary_confirm_data = array();
		$ary_confirm_data = array(
			'select_template'	=> empty($validated['select_template']) ? null : $validated['select_template'],		// 対象テンプレート
			'type'				=> $validated['type'],																// 事業タイプ
			'send_type'			=> $validated['send_type'],															// 送信種別
			'title'				=> empty($validated['title']) ? null : $validated['title'],							// 件名
			'send_required'		=> $send_required_flg,																// 強制送信フラグ
			'head_line'			=> $head_line_flg,																	// 先頭行フラグ
			'text'				=> $validated['text'],																// 本文
			'import_flg'		=> $validated['import_flg'],														// 取り込み方法
			'str_num'			=> empty($validated['str_num']) ? null : $validated['str_num'],						// 文字数
			'import_method'		=> $validated['import_method'],														// 取り込み方法
			'customer_no'		=>  empty($validated['customer_no']) ? null : $validated['customer_no'],			// 会員ID
		);

		if (!empty($ary_check_result['msg']))
		{
			// エラー
			return redirect()->action('Send\SendController@indexId', ['id' => $ary_confirm_data['select_template']])->with('error_msg', $ary_check_result['msg'])->withInput($ary_confirm_data);
		}
		else if (!empty($ary_check_result['ERROR']))
		{
			// エラー行数
			$str_error_num = implode($ary_check_result['ERROR'], ',');

			return redirect()->action('Send\SendController@indexId', ['id' => $ary_confirm_data['select_template']])->with('send_error', $str_error_num)->withInput($ary_confirm_data);
		}
		else if (!empty($ary_check_result['SUCCESS']) || $validated['import_method'] == config('const.IMPORT_METHOD_SIMPLE'))
		{
			// 成功の場合

			if ($validated['import_method'] == config('const.IMPORT_METHOD_CSV'))
			{
				// CSVから取得した場合
				// バリデーションチェックAPIに送る配列作成
				$ary_valid = $this->aryCreateValid($validated, $ary_check_result['SUCCESS']);
			}
			else
			{
				// 1件のみ送信する場合
				$ary_valid = $this->aryCreateValid($validated);
			}

			// バリデーションチェックapiへ
			if ($validated['send_type'] == config('const.SEND_TYPE_MAIL'))
			{
				// メールの場合
				$ary_api_valided = CommonFunction::getApi($ary_valid, 'valid_mail');
			}
			else
			{
				// smsの場合
				$ary_api_valided = CommonFunction::getApi($ary_valid, 'valid_sms');
			}

			switch ($ary_api_valided['apiStatus']) {
				case config('const.api_status_code.SUCCESS') :
					// 成功した場合
					break;
				case config('const.api_status_code.INTERNAL_SERVER_ERROR') :
					// 500エラー
					return redirect()->action('Send\SendController@indexId', ['id' => $ary_confirm_data['select_template']])->with('error_msg', config('const.MSG_INTERNAL_SERVER_ERROR'))->withInput($ary_confirm_data);
					break;
				default :
					// その他エラー
					return redirect()->action('Send\SendController@indexId', ['id' => $ary_confirm_data['select_template']])->with('error_msg', config('const.MSG_FORMAT_ERROR'))->withInput($ary_confirm_data);
					break;
			}
		}

		// apiから返却されたエラー内容精査
		$ary_valid_error = array();
		$ary_valid_error = CommonFunction::apiErrorCodeJapaneseCsv($ary_api_valided, $head_line_flg, 'faliReason');

		// セッションに入れる
		session()->put('ary_valid', $ary_valid);
		session()->put('ary_error_data', $ary_valid_error);

		// 配信登録可能件数
		if (!empty($ary_check_result['SUCCESS']))
		{
			// csvから取得した場合
			$int_success_cnt = count($ary_check_result['SUCCESS']) - count($ary_valid_error);
		}
		else
		{
			// 1通のみ
			if (empty($ary_valid_error))
			{
				$int_success_cnt = 1;
			}
			else
			{
				$int_success_cnt = 0;
			}
		}

		return view('send.confirm',
			[
				'ary_confirm_data'	=> $ary_confirm_data,		// postデータ
				'ary_valid_error'	=> $ary_valid_error,		// バリデーションエラー配列
				'int_error_cnt'		=> count($ary_valid_error),	// バリデーションエラー数
				'int_success_cnt'	=> $int_success_cnt,		// 配信登録可能件数
			]
		);
	}

	public function sendConfirm (Request $request)
	{
		// 押下したボタンチェック
		$action = $request->get('action', 'back');

		$input = $request->except('action');

		if ($action == 'submit')
		{
			// セッションから送信対象データを取り出す
			$ary_valid = session()->get('ary_valid');
			// リクエストデータを取得する
			$ary_request = $request->all();

			// 送信処理
			if ($ary_request['send_type'] == 1)
			{
				// メールの場合
				$ary_send_result = CommonFunction::getApi($ary_valid, 'send_mail');

			}
			else if ($ary_request['send_type'] == 2)
			{
				// SMSの場合
				$ary_send_result = CommonFunction::getApi($ary_valid, 'send_sms');
			}
			else if ($ary_request['send_type'] == 3)
			{
				// LINEの場合
				// 現在使用不可の為リダイレクト
				session()->put('send_error', '1');
			}

			if (empty($ary_send_result) || isset($ary_send_result) && $ary_send_result['apiStatus'] != 200)
			{
				// 結果がエラーの場合
				session()->put('send_error', '1');
			}

			// セッション削除
			session()->forget('ary_valid');
			session()->forget('ary_error_data');

			// セッションに最新のリクエスト結果を格納
			session()->put('ary_latest_send_result', $ary_send_result);

			return redirect()->action('Send\SendController@complete');
		}
		else
		{
			// 前の画面に戻る
			return redirect()->action('Send\SendController@indexId', ['id' => $input['select_template']])->withInput($input);
		}
	}

	public function complete()
	{
		// apiの結果をセッションから取り出す
		$ary_latest_send_result = session()->get('ary_latest_send_result');
		// 送信エラーフラグを取得
		$send_error_flg = session()->get('send_error');
		// 先頭フラグ
		$head_line_flg = session()->get('head_line_flg');

		// セッション削除
		session()->forget('ary_latest_send_result');
		session()->forget('send_error');
		session()->forget('head_line_flg');

		if (empty($send_error_flg) && !empty($ary_latest_send_result))
		{
			// 送信成功した場合
			return view('send.complete',
				[
					'request_id'	=> $ary_latest_send_result['requestId'],
				]);
		}
		else
		{
			return view('send.error',
				[
					'result_list'	=> empty($ary_latest_send_result['errorKey']) ? array() : $ary_latest_send_result['errorKey'],
					'head_line_flg'	=> $head_line_flg,
				]);
		}

	}

	/**
	 * テンプレートリストを取得
	 * @param Request $template_id
	 * @return array
	 */
	public function getSendTemplate(Request $template_id)
	{
		// 対象のテンプレートを取得
		return MailTemplate::where('id', $template_id['id'])
										->where('del_flg', 0)
										->get([
											'id',
											'type',
											DB::connection('mysql_mail')->raw("(CASE type
												WHEN 1 THEN 'kireimo'
												WHEN 2 THEN 'mens kireimo'
												WHEN 3 THEN 'slenda'
												WHEN 4 THEN 'vielis'
												ELSE '-'
												END) AS type_name"),
											'title',
											'text',
											'send_type',
											'url',
											DB::connection('mysql_mail')->raw("(CASE send_type
												WHEN 1 THEN 'メール'
												WHEN 2 THEN 'SMS'
												WHEN 3 THEN 'LINE'
												END) AS send_type_name"),
										]);
	}

	public function aryCreateValid($param, $ary_csv_check_result = '')
	{
		// メールステータス
		if ($param['import_flg'] == config('const.send_param_type.TEL_NO'))
		{
			// 電話番号の場合は強制的に全て送信する
			$mail_status = config('const.mail_status.SEND_ALL_OK');
		}
		else
		{
			// 会員番号の場合は「配信拒否者に送付しない」を見る（on:送信しないので0（SEND_OK）を設定、off:送信するため1（SEND_ALL_OK）を設定）
			$mail_status	= empty($param['send_required']) ? config('const.mail_status.SEND_ALL_OK') : config('const.mail_status.SEND_OK');
		}

		// 先頭行チェック
		$head_flg = empty($param['head_line']) ? false : true;

		// 返却用データ
		$ary_data = array();

		// 子要素
		$ary_sub_data = array();

		// 先頭行を含まない場合
		if (!empty($ary_csv_check_result))
		{
			if(!$head_flg)
			{
				unset($ary_csv_check_result[0]);
			}

			$loop_count = 0;
			foreach ($ary_csv_check_result as $ary_value)
			{
				$i = 0;
				foreach ($ary_value as $rec)
				{
					if ($i == 0)
					{
						// 1列目の処理
						if ($param['import_flg'] == config('const.send_param_type.USER_PARAM'))
						{
							if ($param['send_type'] == config('const.SEND_TYPE_MAIL'))
							{
								// メールアドレスの場合
								$ary_sub_data[$loop_count]['toAddress'] = mb_convert_encoding($rec, 'UTF-8', config('const.mb_code_target'));
							}
							else if ($param['send_type'] == config('const.SEND_TYPE_SMS'))
							{
								// 電話番号の場合、ハイフンを除外する
								$ary_sub_data[$loop_count]['toTel'] = str_replace('-', '', mb_convert_encoding($rec, 'UTF-8', config('const.mb_code_target')));
							}
						}
						else
						{
							// 顧客IDの場合
							$ary_sub_data[$loop_count]['customerNo'] = mb_convert_encoding($rec, 'UTF-8', config('const.mb_code_target'));
						}
					}
					else
					{
						// 2列目以降の処理
						if ($i > config('const.send_insert_max_num.' . $param['send_type']))
						{
							//  上限以上は追加
							break;
						}
						else
						{
							$ary_sub_data[$loop_count]['insert' . $i] = mb_convert_encoding($rec, 'UTF-8', config('const.mb_code_target'));
						}
					}
					$i++;
				}
				$loop_count++;
			}
		}
		else
		{
			// 1通のみ
			$ary_sub_data[]['customerNo'] = mb_convert_encoding($param['customer_no'], 'UTF-8', config('const.mb_code_target'));
			$param['import_flg'] = 2; // 会員番号固定
		}

		if ($param['send_type'] == config('const.SEND_TYPE_MAIL'))
		{
			// MAIL
			$ary_data = array(
				'targetType'		=> mb_convert_encoding(config('const.type.' . $param['type']),			'UTF-8', config('const.mb_code_target')),	// 送信先タイプ（string)
				'textMode'			=> mb_convert_encoding(config('const.MAIL_TYPE_PLANE'),					'UTF-8', config('const.mb_code_target')),	// メールタイプ（string) 第一次フェーズ一旦プレーンテキストのみ対応
				'sendParamType'		=> (int)mb_convert_encoding($param['import_flg'],						'UTF-8', config('const.mb_code_target')),	// パラメータタイプ（int)
				'mailStatus'		=> (int)mb_convert_encoding($mail_status,								'UTF-8', config('const.mb_code_target')),	// メールステータス（int)
				'templateId'		=> (int)mb_convert_encoding($param['select_template'],					'UTF-8', config('const.mb_code_target')),	// テンプレートID（int)
				'param'				=> $ary_sub_data,																									// オブジェクト（array)
			);
		}
		else if ($param['send_type'] == config('const.SEND_TYPE_SMS'))
		{
			// SMS
			$ary_data = array(
				'targetType'		=> mb_convert_encoding(config('const.type.' . $param['type']),			'UTF-8', config('const.mb_code_target')),	// 送信先タイプ（string)
				'sendParamType'		=> (int)mb_convert_encoding($param['import_flg'],						'UTF-8', config('const.mb_code_target')),	// パラメータタイプ（int)
				'mailStatus'		=> (int)mb_convert_encoding($mail_status,								'UTF-8', config('const.mb_code_target')),	// メールステータス（int)
				'templateId'		=> (int)mb_convert_encoding($param['select_template'],					'UTF-8', config('const.mb_code_target')),	// テンプレートID（int)
				'param'				=> $ary_sub_data,																									// オブジェクト（array)
			);
		}

		return $ary_data;
	}

	public function getCsv()
	{
		// セッションからエラーデータを取得する
		$ary_error_data = session()->get('ary_error_data');

		CommonCsv::putErrorCsvTmp($ary_error_data, 'faliReason');
	}

	public function addValidation($param)
	{
		return false;
	}
}