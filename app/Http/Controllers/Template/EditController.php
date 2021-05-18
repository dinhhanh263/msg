<?php

namespace App\Http\Controllers\Template;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\Template\TemplateEditRequest;
use App\Models\MailTemplate;
use Illuminate\Session\SessionManager;
use App\Library\CommonFunction;
use Illuminate\Support\ViewErrorBag;
use Validator;


class EditController extends Controller
{
	// メンバ変数
	public $type;			// 送信元タイプ
	public $send_type;		// 送信種別
	public $template_list;	// 複製対象のテンプレート取得

	public function __construct(Request $request)
	{
		// ログインチェック
		CommonFunction::checkLogin();

		// 共通変数定義
		$this->type				= config('const.type');					// 送信元タイプ
		$this->send_type		= config('const.send_type');			// 送信種別
		$this->template_list	= CommonFunction::getTemplateList();	// テンプレート取得
		$this->ary_url_list		= CommonFunction::urlList();			// URLリスト
	}

	public function index(Request $request)
	{
		// セッションからメッセージを取り出す
		$message = session()->get('template_msg');

		// セッションを削除
		session()->forget('template_msg');

		$label_id = null;

		// getパラメータが付いている場合(一覧画面から遷移されてきた場合)
		if ($request->get('from') == 'list')
		{
			// old_inputを削除
			session()->forget('_old_input');
		}
		else if (!empty($request->get('label_id')))
		{
			$label_id = $request->get('label_id');
		}

		if ($request->get('submit_flg') == 2)
		{
			// 入力値をold_inputに入れる
			session()->put('_old_input', $request->all());

			$this->template_list = CommonFunction::getTemplateList($request->get('send_type'));
		}

		return view('template.edit.index',
			[
				'message'		=> $message,
				'template_list'	=> $this->template_list,
				'type'			=> $this->type,
				'send_type'		=> $this->send_type,
				'url_list'		=> $this->ary_url_list,
				'label_id'		=> $label_id,
			])->withInput(session()->get('_old_input'));
	}

	public function createConfirm(TemplateEditRequest $request)
	{
		// バリデーション完了後の値を取り出す
		$validated = $request->validated();

		// セッションに保存されているユーザー情報を取得する
		$user_data = session('users');

		$replace_check = CommonFunction::checkReplaceString($validated['text'], $validated['send_type']);

		if (!empty($replace_check))
		{
			// セッションにメッセージ格納
			$template_msg['ERROR'] = $replace_check;
			session()->put('template_msg', $template_msg);

			return redirect()->action('Template\EditController@index')->withInput($validated);
		}

		// URLチェック（追加バリデーション）
		if (!empty($validated['url']) && !preg_match('/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $validated['url']))
		{
			$this->validate($request,
				[
					'url' => ['regex:/^(http|https).*/'],
				],
				[
					'url.regex' => 'URLはhttpまたはhttpsから始まる文字列にしてください。'
				]);

			return view('template.edit.index')->withInput($validated);
		}

		// insert
		if ($this->insert($validated, $user_data))
		{
			// 成功した場合
			// セッションにメッセージ格納
			$template_msg['SUCCESS'] = 'テンプレートの作成が完了しました。';
			session()->put('template_msg', $template_msg);

			return redirect()->action('Template\ListController@index');
		} else {
			// セッションにメッセージ格納
			$template_msg['ERROR'] = 'テンプレートの作成に失敗しました。';
			session()->put('template_msg', $template_msg);

			return view('template.edit.index')->withInput($validated);
		}
	}

	public function editConfirm(TemplateEditRequest $request, $id)
	{
		// バリデーション完了後の値を取り出す
		$validated = $request->validated();

		// セッションに保存されているユーザー情報を取得する
		$user_data = session('users');

		$replace_check = CommonFunction::checkReplaceString($validated['text'], $validated['send_type']);

		if (!empty($replace_check))
		{
			// セッションにメッセージ格納
			$template_msg['ERROR'] = $replace_check;
			session()->put('template_msg', $template_msg);

			return redirect()->action('Template\EditController@edit', ['id' => $id])->withInput($validated);
		}

		// URLチェック（追加バリデーション）
		if (!empty($validated['url']) && !preg_match('/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $validated['url']))
		{
			$this->validate($request,
				[
					'url' => ['regex:/^(http|https).*/'],
				],
				[
					'url.regex' => 'URLはhttpまたはhttpsから始まる文字列にしてください。'
				]);

			return redirect()->action('Template\EditController@edit', ['id' => $id])->withInput($validated);
		}

		// update
		if ($this->update($id, $validated, $user_data))
		{
			// 成功した場合

			// セッションにメッセージ格納
			$template_msg['SUCCESS'] = 'テンプレートの編集が完了しました。';
			session()->put('template_msg', $template_msg);

			return redirect()->action('Template\ListController@index');
		}
		else
		{
			// 失敗した場合

			// セッションにメッセージ格納
			$template_msg['ERROR'] = 'テンプレートの編集に失敗しました。';
			session()->put('template_msg', $template_msg);

			return redirect()->action('Template\EditController@edit', ['id' => $id])->withInput($validated);
		}
	}

	/**
	 * 編集ページ
	 *
	 * @param unknown $id
	 * @return unknown|\Illuminate\View\View|\Illuminate\Contracts\View\Factory
	 */
	public function edit($id)
	{
		// セッションからメッセージを取り出す
		$message = session()->get('template_msg');

		// セッションを削除
		session()->forget('template_msg');

		// ユーザー情報を取得する
		$ary_user_data = session()->get('users');

		// 編集対象のレコードを取得
		$ary_edit_template = MailTemplate::where('id', $id)->where('del_flg', 0)->get()->toArray();

		// 一覧へリダイレクト処理
		// 条件1:取得結果が空
		// 条件2:ADMINユーザー以外 かつ グループコードが一致しない
		if (empty($ary_edit_template) ||
			($ary_user_data['login_id'] != config('const.department_code.DEP_CODE_ADMIN') && $ary_user_data['groupCd'] != $ary_edit_template[0]['group_cd']))
		{
			// 存在しない場合は一覧へ
			return redirect()->action('Template\ListController@index');
		}

		// 編集画面へ遷移する
		return view('template.edit.edit',
			[
				'ary_edit_template'	=> $ary_edit_template,
				'type'				=> $this->type,
				'send_type'			=> $this->send_type,
				'url_list'			=> $this->ary_url_list,
				'message'			=> $message,
			]);
	}

	public function getCopyTemplate(Request $template_id)
	{
		// 対象のテンプレートを取得
		$template_data = MailTemplate::where('id', $template_id['id'])->where('del_flg', 0)->get();

		return $template_data;
	}

	public function complete()
	{
		return view('template.edit.complete');
	}

	public function insert($validated, $user_data)
	{
		// パラメータ修正
		list($url, $upload_file_list_id) = $this->urlCheck($validated);

		// insert処理
		return \App\Models\MailTemplate::create([
			'template_name'			=> preg_replace('/[ 　]+$/u', '', preg_replace('/^[ 　]+/u', '', $validated['template_name'])),								// テンプレート名（*)
			'type'					=> $validated['type'],																										// 送信元タイプ（*）
			'title'					=> empty($validated['title']) ? null : preg_replace('/[ 　]+$/u', '', preg_replace('/^[ 　]+/u', '', $validated['title'])),	// 件名（*メールのみ）
			'text'					=> preg_replace('/[ 　]+$/u', '', preg_replace('/^[ 　]+/u', '', $validated['text'])),										// 本文（*)
			'url'					=> $url,																													// URL(SMSのみ)
			'upload_file_list_id'	=> $upload_file_list_id,																									// upload_file_list.id
			'text_mode'				=> empty($validated['text_mode']) ? 0 : $validated['text_mode'],															// テキストタイプ（デフォルト:0（プレーン））
			'send_type'				=> $validated['send_type'],																									// 送信種別（*）
			'send_class'			=> empty($validated['send_class']) ? 0 : $validated['send_class'],															// 送信区分（デフォルト:0（指定なし））
			'send_timing'			=> empty($validated['send_timing']) ? 0 : $validated['send_timing'],														// 送信タイミング（デフォルト:0（指定なし））
			'send_time'				=> empty($validated['send_time']) ? null : $validated['send_time'],															// 送信時間（送信タイミングが選択されている場合のみ）
			'login_id'				=> $user_data['login_id'],																									// 登録者（*）
			'group_cd'				=> $user_data['groupCd'],																									// 部署コード
			'created_at'			=> now(),																													// 登録時間（*）
			'updated_at'			=> now(),																													// 更新時間（*）
		]);
	}

	public function update($id, $validated, $user_data)
	{
		// 更新する値
		// 件名はメールの時のみ
		if ($validated['send_type'] == 1)
		{
			return MailTemplate::
				where('id', $id)
				->update([
					'template_name'	=> preg_replace('/[ 　]+$/u', '', preg_replace('/^[ 　]+/u', '', $validated['template_name'])),
					'text'			=> preg_replace('/[ 　]+$/u', '', preg_replace('/^[ 　]+/u', '', $validated['text'])),
					'title'			=> preg_replace('/[ 　]+$/u', '', preg_replace('/^[ 　]+/u', '', $validated['title'])),
				]);
		}
		else
		{
			// パラメータ修正
			list($url, $upload_file_list_id) = $this->urlCheck($validated);

			return MailTemplate::
			where('id', $id)
			->update([
				'template_name'			=> preg_replace('/[ 　]+$/u', '', preg_replace('/^[ 　]+/u', '', $validated['template_name'])),
				'text'					=> preg_replace('/[ 　]+$/u', '', preg_replace('/^[ 　]+/u', '', $validated['text'])),
				'url'					=> $url,
				'upload_file_list_id'	=> $upload_file_list_id,
			]);
		}
	}

	public function urlCheck($validated)
	{
		$url = null;
		$upload_file_list_id = null;
		if ($validated['send_type'] == config('const.SEND_TYPE_SMS'))
		{
			if ($validated['url_type'] == 1 && empty($validated['url_choice']))
			{
				// ファイル選択かつ、ファイルが指定されていない場合
				$url = null;
			}
			else {
				// url
				$url = empty($validated['url']) ? null : preg_replace('/[ 　]+$/u', '', preg_replace('/^[ 　]+/u', '', $validated['url']));
			}

			// upload_file_list
			if ($validated['url_type'] == 1)
			{
				// アップロードファイルの場合、upload_file_list_idを登録する
				$upload_file_list_id = $validated['url_choice'];
			}

			// SMSの場合、textの改行コードを削除する
			$validated['text'] = str_replace(array("\r\n", "\r", "\n"), '', $validated['text']);
		}

		return array($url, $upload_file_list_id);
	}
}
