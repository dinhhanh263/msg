<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpdateFileList extends Model
{
	// 接続先定義
	protected $connection = 'mysql_mail';

	// テーブル名定義
	protected $table = 'upload_file_list';

	const CREATED_AT = 'reg_date';
	const UPDATED_AT = 'edit_date';

	// 更新可能カラム
	protected $fillable = [
		'label',
		'file_name',
		'group_cd',
	];
}
