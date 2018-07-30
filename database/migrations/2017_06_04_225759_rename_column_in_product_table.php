<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColumnInProductTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'products', function ( Blueprint $t ) {
            $t->renameColumn( 'name', 'title' );
            $t->renameColumn( 'period', 'renew_period' );
            $t->renameColumn( 'initialPrice', 'initial_price' );
            $t->renameColumn( 'periodPrice', 'renew_price' );
            $t->integer( 'sort' )->after( 'currency' )->defaultValue( 1 );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'products', function ( Blueprint $t ) {
            $t->renameColumn( 'title', 'name' );
            $t->renameColumn( 'renew_period', 'period' );
            $t->renameColumn( 'initial_price', 'initialPrice' );
            $t->renameColumn( 'renew_price', 'periodPrice' );
            $t->removeColumn( 'sort' );
        } );
    }
}
