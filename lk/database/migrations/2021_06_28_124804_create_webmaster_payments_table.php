<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebmasterPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webmaster_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->json('requisite'); // JSON реквизиты карты или ИП
            $table->bigInteger('user_id')->unsigned(); // USER ID
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
            $table->enum('type', ['CARD', 'REQUISITE']); //Тип платёжки
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
        Schema::dropIfExists('webmaster_payments');
    }
}
