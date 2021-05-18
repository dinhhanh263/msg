<?php

//**********************************************************************
// 定数
//**********************************************************************

return [
	// apiのurl
	'api_url' => env('API_URL'),

	// apiのtoken
	'api_token'	=> env('API_TOKEN'),

	'log_dir' =>
		[
			'log_base' => '/logs/msg/',
		],

	// api返却ステータスコード
	'api_status_code' =>
		[
			'SUCCESS'				=> 200,	// 成功
			'NO_CONTENT'			=> 204, // 成功したが、何も返すものがない
			'BAD_REQUEST'			=> 400,	// 失敗
			'UNAUTHRIZED'			=> 401, // 認証が必要
			'FORBIDDEN'				=> 403,	// アクセス権がない
			'NOT_FOUND'				=> 404,	// リソースが存在しない。URL不正
			'INTERNAL_SERVER_ERROR'	=> 500,	// サーバーエラー
		],

	'MSG_INTERNAL_SERVER_ERROR'	=> 'システムエラーが発生しました。情報システム部へ連絡してください。',
	'MSG_FORMAT_ERROR'			=> 'フォーマットチェックができませんでした。時間を置いて再度お試しください。',

	// apiエラーコード
	'api_error_code' =>
		[
			'E5000' => '認証エラー',
			'E5001' => 'システムエラー',
			'E5002' => '対象データが存在しません',
			'E5003' => 'DBデータ重複',
			'E9000' => '型が不正です',
			'E9001' => '必須項目が存在しません',
			'E9002' => 'フォーマットが不正です',
			'E9003' => '有効な値ではありません',
			'E9004' => '桁数が不正です',
			'E9005' => 'データが重複しています',
			'E9006' => '送信対象が0件です',
			'E9007' => 'メールステータスが不正です',
			'E9008' => '件数不正',
			'E9009' => '差し込み文字不整合',
		],

	// api uri
	'api_sub' =>
		[
			'auth'			=> 'auth/mailAdminAuth',		// ログイン
			'valid_sms'		=> 'action/sms/checkValid',		// バリデーションチェック(SMS)
			'send_sms'		=> 'action/sms/send',			// sms送信
			'valid_mail'	=> 'action/mail/checkValid',	// バリデーションチェック(MAIL)
			'send_mail'		=> 'action/mail/send',			// メール送信
		],

	// smsのurl
	'sms_url' => env('SMSLINK_URL'),

	// apiのtoken
	'sms_token'	=> env('SMSLINK_TOKEN'),

	// sms linkリクエスト用
	'sms_uri' =>
		[
			'DELIVERY_RESULT'	=> 'delivery_results',
		],

	// sms links ステータスコード
	'sms_status_code' =>
		[
			'UNDER_RESERVATION'	=>	50,		// 予約中
			'DURING_DELIVERY'	=>	100,	// 配信中
			'SEND_COMPLETE'		=>	200,	// 送達
			'SEND_CANCEL'		=>	300,	// 配信取り消し
			'SEND_NO'			=>	700,	// 配信不可
			'SEND_NG'			=>	900,	// 配信失敗
		],

	// sms linksエラー
	'sms_eror' =>
		[
			'RST200001'	=> '配信対象に登録しました。',
			'RST200002'	=> '携帯電話番号がない為、配信対象に登録できませんでした。',
			'RST200003'	=> '携帯電話番号が11桁でない為、配信対象に登録できませんでした。',
			'RST200004'	=> '携帯電話番号が重複した為、配信対象に登録できませんでした。',
			'RST200005'	=> '配信不可な電話番号の為、配信対象に登録できませんでした。',
			'RST200006'	=> '宛先項目が255文字を超えてる為、配信対象に登録できませんでした。',
			'RST200007'	=> '差込項目に使用できない文字がある為、配信対象に登録できませんでした。',
			'RST200008'	=> '本文が差込項目含めて660文字を超えてる為、廃止対象に登録できませんでした。',
		],

	// 送信元タイプ
	'type' =>
		[
			1	=> 'kireimo',
			//2	=> 'mens kireimo',
			//3	=> 'slenda',
			//4	=> 'staff',
		],

	'TYPE_KIREIMO'	=> 1,
	//'TYPE_STAFF'	=> 4,


	// 送信種別
	'send_type' =>
		[
			1	=> 'メール',
			2	=> 'SMS',
			//3	=> 'LINE',
		],

	'SEND_TYPE_MAIL'	=> 1,
	'SEND_TYPE_SMS'		=> 2,
	'SEND_TYPE_LINE'	=> 3,

	// paramバリデーションチェック要否
	'need_param_valid' =>
		[
			'UNNECESSARY'	=> 0,
			'NECESSARY'		=> 1,
		],

	// パラメータータイプ
	'send_param_type' =>
		[
			'USER_PARAM'	=> 1,
			'CUSTOMER_ID'	=> 2,
		],

	// メールステータス
	'mail_status' =>
		[
			'SEND_OK'		=> 0,
			'SEND_ALL_OK'	=> 1,
		],

	// 取り込み方法
	'IMPORT_METHOD_CSV'		=> 1,	// csv取り込み
	'IMPORT_METHOD_SIMPLE'	=> 2,	// 1通のみ

	// メールタイプ
	'MAIL_TYPE_PLANE'	=> 'plane',	// プレーン
	'MAIL_TYPE_HTML'	=> 'html',	// HTML

	// バリデーションチェック後表示件数
	'valided_disp_num' => 10,

	// 部署コード
	'department_code' =>
		[
			'DEP_CODE_ADMIN'	=> 'ADMIN',
		],

	// 70文字超えた場合のメッセージ
	'sms_warning_msg' => '※70文字を超えた場合、追加で料金が発生する場合があります。',

	// 配信登録限度数
	'sms_send_max_num'	=>
		[
			1	=> null,
			2	=> 10000,	// sms送信上限
		],

	// 差し込み上限数
	'send_insert_max_num' =>
		[
			1	=> 10,	// MAIL
			2	=> 5,	// SMS
		],

	// 文字コード変換対象
	'mb_code_target' => 'UTF-8, sjis, sjis-win',

	// テンプレート検索条件
	'template_send_history' =>
		[
			1	=> '配信登録履歴なし',
			2	=> '配信登録履歴あり',
		],

	// 配信結果検索条件
	'regist_status' =>
		[
			1	=> '成功',
			2	=> '失敗',
		],
];