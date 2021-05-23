<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsDisputTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('disput_type', function (Blueprint $table) {
            $table->bigInteger('disput_type_id')->default(5);
            $table->bigInteger('order_by')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('disput_type', function (Blueprint $table) {
            //
        });
    }
}
