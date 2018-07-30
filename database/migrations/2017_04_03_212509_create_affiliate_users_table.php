<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAffiliateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliate_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index()->uniq();
            $table->string('affiliate_name');
            $table->boolean('accepted_rules')->default(false);
            $table->integer('parent_id')->nulable();
            $table->float('total_income')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'affiliate_users' );
    }
}
