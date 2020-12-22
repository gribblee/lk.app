<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemoBidTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demo_bid', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('bid_id')->nullable();
            $table->foreign('bid_id')
                ->references('id')
                ->on('bids')
                ->onUpdate('restrict')
                ->onDelete('set null');

            $table->unsignedBigInteger('user_id')->nullable();   
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('restrict')
                ->onDelete('set null');

            $table->json('request');

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
        Schema::dropIfExists('demo_bid');
    }
}
