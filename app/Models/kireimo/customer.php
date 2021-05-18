<?php

namespace App\Models\kireimo;

use Illuminate\Database\Eloquent\Model;

class customer extends Model
{
	// 接続先定義
	protected $connection = 'mysql_kireimo';

	// テーブル名定義
    protected $table = 'customer';
}
