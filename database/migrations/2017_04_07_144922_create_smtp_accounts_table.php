<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmtpAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('smtp_accounts', function(Blueprint $t){
            $t->increments('id');
            $t->integer('user_id');
            $t->integer('proxy_id')->default(null);

            $t->string('email');
            $t->string('password');

            $t->string('host');
            $t->string('port')->default(587);

            $t->boolean('enabled')->default(0);

            $t->string('ssl_enabled')->default('tls');
            $t->integer('period')->default(3600*24);

            $t->timestamp('last_use');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('smtp_accounts');
    }
}
