<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailTemplate extends Model
{
	// 接続先定義
	protected $connection = 'mysql_mail';

	// テーブル名定義
	protected $table = 'mail_template';

	// 更新可能カラム
	protected $fillable = [
		'template_name',
		'type',
		'title',
		'text',
		'url',
		'upload_file_list_id',
		'from_address',
		'text_mode',
		'send_type',
		'send_class',
		'send_timing',
		'send_time',
		'login_id',
		'group_cd',
		'created_at',
		'updated_at',
	];
}
