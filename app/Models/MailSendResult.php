<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailSendResult extends Model
{
	// 接続先定義
	protected $connection = 'mysql_mail';

	// テーブル名定義
	protected $table = 'mail_send_result';
}
