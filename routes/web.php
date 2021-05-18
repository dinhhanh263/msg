<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//***************************************************************************
// ログイン
//***************************************************************************
Route::get('/',								'Login\LoginController@index')->name('login');								// ログイントップ
Route::post('/login/auth', 					'Login\LoginController@auth')->name('auth');								// 認証
Route::get('/logout', 						'Login\LoginController@logout')->name('logaout');							// ログアウト

//***************************************************************************
// トップ
//***************************************************************************
Route::get('/top', 							'Top\TopController@index')->name('top');

//***************************************************************************
// 送信
//***************************************************************************
Route::post('/send/template',				'Send\SendController@getSendTemplate')->name('selectSendTemplate');			// 送信対象テンプレート取得
Route::post('/send/confirm',				'Send\SendController@confirm')->name('sendConfirm');						// 配信登録確認画面
Route::post('/send/api/confirm',			'Send\SendController@sendConfirm')->name('sendApiConfirm');					// 配信登録処理
Route::get('/send/complete',				'Send\SendController@complete')->name('sendComplete');						// 配信登録完了画面
Route::post('/send/getCsv',					'Send\SendController@getCsv')->name('getCsv');								// バリデーションエラーcsv取得

Route::get('/send/{id}',					'Send\SendController@indexId')->name('sendId');								// 配信登録トップ

//***************************************************************************
// テンプレート
//***************************************************************************
Route::get('/template',						'Template\ListController@index')->name('templateList');						// テンプレートリスト
Route::post('/template',					'Template\ListController@index')->name('templateSearch');					// テンプレートリスト

Route::get('/template/edit',				'Template\EditController@index')->name('templateCreate');					// テンプレート作成
Route::post('/template/edit',				'Template\EditController@index')->name('templateCreateSearch');				// テンプレート作成


Route::post('/template/confirm',			'Template\EditController@createConfirm')->name('templateCreateConfirm');	// テンプレート作成確認
Route::post('/template/copylist',			'Template\EditController@getCopyTemplate')->name('templateGetCopyList');	// 複製元テンプレート取得

Route::get('/template/edit/{id}',			'Template\EditController@edit')->name('templateEdit');						// テンプレート編集
Route::post('/template/confirm/{id}',		'Template\EditController@editConfirm')->name('templateEditConfirm');		// テンプレート編集確認

//***************************************************************************
// 送信履歴
//***************************************************************************
Route::get('/history',						'History\ListController@index')->name('historyList');						// 送信履歴一覧
Route::post('/history',						'History\ListController@index')->name('historySearch');						// 送信履歴一覧

Route::get('/history/detail/{request_id}',	'History\DetailController@index')->name('historyDetail');					// 送信履詳細
Route::post('/history/getCsv',				'History\DetailController@getCsv')->name('historyGetCsv');					// 送信履詳細

//***************************************************************************
// アップロード
//***************************************************************************
Route::get('/upload',						'Upload\UploadController@index')->name('uploadIndex');						// ファイルアップロード画面
Route::post('/upload',						'Upload\UploadController@uploadFile')->name('uploadPost');					// ファイルアップロード処理


//***************************************************************************
// その他
//***************************************************************************
Route::get('/help', 						'Help\HelpController@index')->name('help');									// ヘルプ


