<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPamAccountAddLoginIpField extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('pam_account', function (Blueprint $table) {
			$table->string('login_ip', 20)->default('')->comment('注册IP')->after('login_times');
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
			$table->dropColumn('login_ip');
		});
	}
}
