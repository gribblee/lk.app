<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_order', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedbigInteger('store_id');
            $table->foreign('store_id')
                ->references('id')
                ->on('store')
                ->onUpdate('RESTRICT')
                ->onDelete('SET NULL');
            $table->bigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('RESTRICT')
                ->onDelete('SET NULL');
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
        Schema::dropIfExists('store_order');
    }
}
