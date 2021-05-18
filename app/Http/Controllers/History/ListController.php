<?php

namespace App\Http\Controllers\History;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\MailSendResult;
use App\Library\CommonFunction;

class ListController extends Controller
{
	// 検索条件
	public $send_type;		// 配信種別
	public $regist_status;	// 配信結果

	public function __construct()
	{
		// ログインチェック
		CommonFunction::checkLogin();

		// 検索項目
		$this->send_type		= config('const.send_type');		// 配信種別
		$this->type				= config('const.type');				// 配信元事業部
		$this->regist_status	= config('const.regist_status');	// 配信結果
	}

	public function index(Request $request)
	{
		// ユーザー情報を取得する
		$ary_user_data = session()->get('users');

		$query = MailSendResult::query();

		// 送信種別が設定されている場合
		if (!empty($request->input('send_type')))
		{
			$query->where('send_type', $request->input('send_type'));
		}

		// 配信元事業部が設定されている場合
		if (!empty($request->input('type')))
		{
			$query->where('type', $request->input('type'));
		}

		// 配信結果
		if (!empty($request->input('regist_status')))
		{
			// 読み替え
			$regist_status = $request->input('regist_status') == 1 ? 0 : 1;

			$query->where('regist_status', $regist_status);
		}

		// ADMINでログインしている場合はどの部署の結果も見れるようにする
		if ($ary_user_data['groupCd'] != config('const.department_code.DEP_CODE_ADMIN'))
		{
			$query->where('group_cd', $ary_user_data['groupCd']);
		}

		$query->leftjoin('mail_template', 'mail_send_result.template_id', '=', 'mail_template.id')->groupBy('mail_send_result.request_id');

		// 3ヶ月以内のデータを取得
		// 3ヶ月前の日付を取得
		$three_month = date("Y-m-d 00:00:00",strtotime("-3 month"));
		// 現在日時
		$now = date('Y-m-d 23:59:59');

		$query->whereBetween('mail_send_result.reg_date', [$three_month, $now]);

		// 送信日でソート
		$query->orderby('mail_send_result.reg_date', 'desc');

		// pagenate
		$ary_mail_send_result = $query->paginate(10);

		session()->put('_old_input', $request->all());

		// 検索条件
		$search = $request->except('page');
		unset($search['_token']);

		// テンプレート一覧画面へ
		return view('history.list.index',
			[
				'send_type'			=> $this->send_type,
				'type'				=> $this->type,
				'regist_status'		=> $this->regist_status,
				'ary_mail_template' => $ary_mail_send_result,
				'request'			=> $search,
			])->withInput(session()->get('_old_input'));
	}
}
