<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToBetSelectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bet_selections', function (Blueprint $table) {
            $table->foreign('bet_id', 'bet_selections_ibfk_1')->references('id')->on('bet')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bet_selections', function (Blueprint $table) {
            $table->dropForeign('bet_selections_ibfk_1');
        });
    }
}
