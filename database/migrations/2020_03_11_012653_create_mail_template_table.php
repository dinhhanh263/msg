<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_mail')->create('mail_template', function (Blueprint $table) {
        	$table->bigIncrements('id')->unsigned()->comment('メールテンプレートID');
        	$table->string('template_name')->comment('テンプレート名');
        	$table->tinyInteger('type')->comment('1:kireimo, 2:mens kireimo, 3:slenda, 4:vielis');
        	$table->string('title')->nullable()->comment('件名（メールの場合のみ使用）');
        	$table->text('text')->nullable()->comment('本文');
        	$table->string('from_address')->nullable()->comment('送信元アドレス');
        	$table->tinyInteger('mime_type')->comment('0:プレーン, 1:リッチ')->default(0);
        	$table->tinyInteger('send_type')->comment('1:メール, 2:SMS, 3:LINE');
        	$table->tinyInteger('send_class')->comment('0:指定なし, 1:来店, 2:契約, 3:返金')->default(0);
        	$table->tinyInteger('send_timing')->comment('0:指定なし, 1:毎分, 2:毎時, 3:日次, 4:週次, 5:月次, 6:年次')->default(0);
        	$table->dateTime('send_time')->nullable()->comment('送信時刻');
        	$table->string('created_user')->comment('作成者');
        	$table->string('updated_user')->comment('更新者');
        	$table->timestamps();
        	$table->tinyInteger('del_flg')->comment('削除フラグ')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    	Schema::connection('mysql_mail')->dropIfExists('mail_template');
    }
}
