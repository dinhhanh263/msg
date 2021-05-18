<?php

namespace App\Http\Controllers\Login;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Library\CommonFunction; // 共通関数

class LoginController extends Controller
{
	/**
	 * ログインチェック
	 * @param Request $request
	 */
	public function __construct(Request $request)
	{
		// ログアウト以外の場合
		if ($request->server->get('REQUEST_URI') != '/logout') {
			// ログインチェック
			$is_users = $request->session()->has('users');

			if ($is_users) {
				// ログイン中
				Redirect::to('top')->send();
			}
		}
	}

	/**
	 * ログイン画面
	 * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
	 */
	public function index()
	{
		// エラーセッションを取得
		$error_flg = empty(session()->get('login_error')) ? 0 : 1;

		// セッション削除
		session()->forget('login_error');

		// ログイン画面へ遷移
		return view('login.index', [
			'error_flg' => $error_flg,
		]);
	}

	/**
	 * ログイン認証処理
	 * @param \App\Http\Requests\Login\LoginRequest $request
	 * @return Redirect
	*/
	public function auth(Request $request)
	{
		// リクエストパラメータを取得
		$param = $request->all();

		// バリデーションチェック
		if ($this->loginValidationCheck($param) == false)
		{
			// セッションに失敗したメッセージを格納
			session()->put('login_error', '1');
			return redirect()->action('Login\LoginController@index');
		}

		// パラメータ編集
		$param_edit['loginId']	= $param['login_id'];
		$param_edit['password']	= $param['password'];

		// apiへポストする
		$user_data = CommonFunction::getApi($param_edit, 'auth');

		// 結果判定
		if ($user_data['apiStatus'] != config('const.api_status_code.SUCCESS'))
		{
			// セッションに失敗したメッセージを格納
			session()->put('login_error', '1');
			return redirect()->action('Login\LoginController@index');
		}

		// セッションに格納する
		$this->setSession($param, (array)$user_data['body'], $request);

		// ログイン後の画面に遷移する
		return redirect()->action('Top\TopController@index');
	}

	/**
	 * ログアウト
	 * @param Request $request
	 * @return Redirect
	 */
	public function logout(Request $request) {
		// セッション削除
		$request->session()->flush();

		return redirect()->action('Login\LoginController@index');
	}

	/**
	 * セットセッション
	 * @param array $param
	 * @param array $request
	*/
	private function setSession($param, $user_data, $request)
	{
		// セッション格納値の設定
		$sesssion_param = array();
		$sesssion_param = array(
			'login_id'	=> $param['login_id'],
		);

		// api取得値をforeachで入れ込む
		foreach ($user_data as $key => $value) {
			$param[$key] = $value;
		}

		// セッションIDの再発行
		$request->session()->regenerate();
		// ユーザー情報の格納
		$request->session()->put('users', $param);
	}

	private function loginValidationCheck($param)
	{
		// バリデーションチェック
		if (!preg_match("/^[a-zA-Z0-9]+$/", $param['login_id']))
		{
			return false;
		}
		else if (!preg_match("/^[a-zA-Z0-9]+$/", $param['password']))
		{
			return false;
		}

		return true;
	}
}