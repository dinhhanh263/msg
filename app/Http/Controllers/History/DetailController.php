<?php

namespace App\Http\Controllers\History;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Library\CommonCsv;
use App\Models\MailSendResult;
use App\Library\CommonFunction;

class DetailController extends Controller
{
	public function __construct()
	{
		// ログインチェック
		CommonFunction::checkLogin();
	}

	public function index($request_id) {

		// ユーザー情報を取得する
		$ary_user_data = session()->get('users');

		$ary_mail_send_result = MailSendResult::leftjoin('mail_template', 'mail_send_result.template_id', '=', 'mail_template.id')
								->where('mail_send_result.request_id', $request_id)
								->get()
								->toArray();

		// 一覧へリダイレクト処理
		// 条件1:取得結果が空
		// 条件2:ADMINユーザー以外
		// 条件3:グループコードが一致しない
		if (empty($ary_mail_send_result) ||
			($ary_user_data['login_id'] != config('const.department_code.DEP_CODE_ADMIN') && $ary_user_data['groupCd'] != $ary_mail_send_result[0]['group_cd']))
		{
			// 存在しない場合は一覧へ
			return redirect()->action('History\ListController@index');
		}

		// smsの場合配信結果を取得
		if ($ary_mail_send_result[0]['send_type'] == config('const.SEND_TYPE_SMS'))
		{
			// 配信結果が700のものを取得
			$param_send_no							= config('const.sms_uri.DELIVERY_RESULT') . '?delivery_id=' . $ary_mail_send_result[0]['delivery_id'] . '&status=' . config('const.sms_status_code.SEND_NO');
			$ary_result_sms_send_no					= CommonFunction::getApiSms($param_send_no);

			// 配信結果が900のもの
			$param_send_ng							= config('const.sms_uri.DELIVERY_RESULT') . '?delivery_id=' . $ary_mail_send_result[0]['delivery_id'] . '&status=' . config('const.sms_status_code.SEND_NG');
			$ary_result_sms_send_ng					= CommonFunction::getApiSms($param_send_ng);

			// 配列の結合
			foreach($ary_result_sms_send_no['contacts'] as $value)
			{
				$ary_mail_send_result['contacts'][] = $value;
			}

			foreach($ary_result_sms_send_ng['contacts'] as $value)
			{
				$ary_mail_send_result['contacts'][] = $value;
			}

			$ary_mail_send_result['total'] = $ary_result_sms_send_no['total'] + $ary_result_sms_send_ng['total'];
		}

		return view('history.detail',
			[
				'ary_mail_send_result'	=> $ary_mail_send_result,
			]
			);
	}
	public function getCsv(Request $request)
	{
		// リクエストの中身を取得
		$request_id = $request->get('request_id');

		// テーブルからエラー内容を取得
		$ary_error_data = MailSendResult::where('request_id', $request_id)->get('regist_fails')->toArray();

		// 取得した内容を配列にしてくっつける
		$ary_edit_data = array();
		foreach ($ary_error_data as $ary_value)
		{
			// jsonを配列に直す
			$ary_edit_json = array();
			$ary_edit_json = json_decode($ary_value['regist_fails'], true);

			foreach ($ary_edit_json as $value)
			{
				$ary_edit_data[] = $value;
			}
		}

		// エラーコードを日本語化する
		$ary_jap_data = CommonFunction::smsErrorValidationErrorJapanese($ary_edit_data);

		// csvを出力
		CommonCsv::putErrorSms($ary_jap_data);
	}
}
