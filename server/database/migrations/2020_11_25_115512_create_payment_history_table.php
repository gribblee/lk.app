<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->enum('type_transaction', [10, 11, 12, 13, 14, 15]);
            $table->bigInteger('paysum');
            $table->bigInteger('paybonus');
            $table->bigInteger('before_balance');
            $table->bigInteger('after_balance');
            $table->bigInteger('before_bonus');
            $table->bigInteger('after_bonus');
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
        Schema::dropIfExists('payment_history');
    }
}
