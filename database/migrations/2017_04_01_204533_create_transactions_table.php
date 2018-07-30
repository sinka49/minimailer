<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'transactions', function ( Blueprint $table ) {
            $table->increments( 'id' );
            $table->integer( 'userId' );

            $table->string( 'operationDescription' ); //description of operation
            $table->integer( 'operationCode' );       // code of operation
            $table->integer( 'operationValue' );      // code of operation
            $table->integer( 'status' );

            $table->float( 'debitAmount' );
            $table->float( 'creditAmount' );
            $table->integer( 'currencyId' );
            $table->float( 'balance' );
            $table->string('serviceResponse');
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'transactions' );
    }
}
