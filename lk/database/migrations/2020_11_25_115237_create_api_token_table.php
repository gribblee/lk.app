<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiTokenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_token', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('hash');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->unsignedBigInteger('count_deals');

            $table->unsignedBigInteger('direction_id');
            $table->foreign('direction_id')
                ->references('id')
                ->on('directions');
            $table->unsignedBigInteger('count_deals');

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
        Schema::dropIfExists('api_token');
    }
}
