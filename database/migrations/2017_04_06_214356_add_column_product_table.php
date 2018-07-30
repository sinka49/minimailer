<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function(Blueprint $t){
            $t->renameColumn('price', 'initialPrice');
            $t->float('periodPrice')->after('price');
            $t->integer('period')->after('periodPrice');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function(Blueprint $t){
            $t->renameColumn('initialPrice', 'price');
            $t->dropColumn('periodPrice');
            $t->dropColumn('period');
        });
    }
}
