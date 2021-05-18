<?php

namespace App\Http\Controllers\Top;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use App\Models\MailSendResult;
use App\Library\CommonFunction;

class TopController extends Controller
{
	public function __construct()
	{
		// ログインチェック
		CommonFunction::checkLogin();

		// 検索項目
		$this->send_type = config('const.send_type');	// 送信種別
	}

	public function index()
	{
		// ユーザー情報を取得する
		$ary_user_data = session()->get('users');

		// クエリーを使用
		$query = MailSendResult::query();

		// mail_send_resultとmail_templateをjoin
		$query->leftjoin('mail_template', 'mail_send_result.template_id', '=', 'mail_template.id');

		// 3ヶ月以内のデータを取得
		// 3ヶ月前の日付を取得
		$three_month = date("Y-m-d 00:00:00",strtotime("-3 month"));
		// 現在日時
		$now = date('Y-m-d 23:59:59');

		$query->whereBetween('mail_send_result.reg_date', [$three_month, $now]);

		// ADMINでログインしている場合はどの部署の結果も見れるようにする
		if ($ary_user_data['login_id'] != config('const.department_code.DEP_CODE_ADMIN'))
		{
			$query->where('mail_template.group_cd', $ary_user_data['groupCd']);
		}

		// リクエストIDでまとめる
		$query->groupBy('mail_send_result.request_id');
		//  配信登録日で並び替え
		$query->orderby('mail_send_result.reg_date', 'desc');
		// 5件のみ表示
		$query->limit(5);

		$ary_latest_result = $query->get()->toArray();

		// トップ画面へ遷移
		return view('top.index',
			[
				'send_type'			=> $this->send_type,
				'ary_latest_result'	=> $ary_latest_result,
			]);
	}
}
