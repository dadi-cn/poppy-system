<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPamAccountAddFieldParentId extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('pam_account', function (Blueprint $table) {
			$table->integer('parent_id')->default(0)->comment('父账号ID')->after('email');
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
			$table->dropColumn('parent_id');
		});
	}
}
