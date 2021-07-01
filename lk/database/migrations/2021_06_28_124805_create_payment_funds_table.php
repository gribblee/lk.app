<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentFundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_funds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->float('amount');
            $table->enum('status', ['CREATE', 'PENDING', 'PAID', 'ERROR', 'CANCEL']); // Статусы выплаты CREATE | PENDING | PAID | ERROR | CANCEL
            /**
             * CREATE - Создано
             * PENDING - Ждёт выплаты
             * PAID - Выплачено
             * ERROR - Ошибка
             * CANCEL - Отмена
             */
            $table->bigInteger('payments_id')->unsigned(); // ID Реквизитов
            $table->foreign('payments_id')
                ->references('id')
                ->on('webmaster_payments')
                ->onDelete('set null');

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
        Schema::dropIfExists('payment_funds');
    }
}
