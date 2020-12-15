<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTokenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_token', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('passphrase', 10); //СМС код
            $table->string('token')->unique(); //СМС токен

            $table->json('response')->nullable();

            $table->unsignedBigInteger('user_id'); //ID пользователя
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users'); //ID пользователя скрепление с таблицей users
            $table->boolean('is_verified')->default(false); //Верифицирован ли sms токен
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
        Schema::dropIfExists('users_token');
    }
}
