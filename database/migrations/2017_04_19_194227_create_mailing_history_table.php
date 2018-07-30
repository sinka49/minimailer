<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailingHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailing_history', function(Blueprint $t){
            $t->increments('id');
            $t->integer('user_id');
            $t->string('smtp_account_email');
            $t->string('recipient_email');
            $t->integer('job_id');
            $t->integer('status');
            $t->timestamp('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mailing_history');
    }
}
