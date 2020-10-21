<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPamAccountAddPlatformFiled extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('pam_account', function (Blueprint $table) {
			$table->string('reg_platform', 15)->default('')->comment('注册平台')->after('login_times');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('pam_account', function (Blueprint $table) {
			$table->dropColumn('reg_platform');
		});
	}
}
