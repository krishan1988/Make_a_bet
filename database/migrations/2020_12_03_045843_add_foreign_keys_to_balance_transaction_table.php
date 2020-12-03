<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToBalanceTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('balance_transaction', function (Blueprint $table) {
            $table->foreign('player_id', 'balance_transaction_ibfk_1')->references('id')->on('player')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('balance_transaction', function (Blueprint $table) {
            $table->dropForeign('balance_transaction_ibfk_1');
        });
    }
}
