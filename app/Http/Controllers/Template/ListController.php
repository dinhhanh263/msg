<?php

namespace App\Http\Controllers\Template;

use App\Http\Controllers\Controller;
use App\Models\MailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Library\CommonFunction;

class ListController extends Controller
{
	// 検索条件
	public $send_type;		// 配信種別
	public $send_history;	// 配信履歴

	public function __construct()
	{
		// ログインチェック
		CommonFunction::checkLogin();

		// 検索項目
		$this->send_type	= config('const.send_type');				// 配信種別
		$this->type			= config('const.type');						// 配信元事業部
		$this->send_history	= config('const.template_send_history');	// 配信履歴
	}

	public function index(Request $request)
	{
		// ユーザー情報を取得する
		$ary_user_data = session()->get('users');

		// メッセージ情報を取得
		// セッションからメッセージを取り出す
		$message = session()->get('template_msg');

		// セッションを削除
		session()->forget('template_msg');

		$query = MailTemplate::query();

		// 配信種別が設定されている場合
		if (!empty($request->input('send_type')))
		{
			$query->where('send_type', $request->input('send_type'));
		}

		// 配信元事業部が設定されている場合
		if (!empty($request->input('type')))
		{
			$query->where('type', $request->input('type'));
		}

		// ADMINでログインしている場合はどの部署の結果も見れるようにする
		if ($ary_user_data['groupCd'] != config('const.department_code.DEP_CODE_ADMIN'))
		{
			$query->where('group_cd', $ary_user_data['groupCd']);
		}

		//  更新日で並び替え
		$query->orderby('updated_at', 'desc');

		// pagenate
		$ary_mail_template = $query->paginate(10);

		session()->put('_old_input', $request->all());

		// 検索条件
		$search = $request->except('page');
		unset($search['_token']);

		// テンプレート一覧画面へ
		return view('template.list.index',
			[
				'send_type'			=> $this->send_type,
				'type'				=> $this->type,
				'send_history'		=> $this->send_history,
				'ary_mail_template' => $ary_mail_template,
				'message'			=> $message,
				'request'			=> $search,
			])->withInput(session()->get('_old_input'));
	}
}
