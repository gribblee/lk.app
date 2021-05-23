<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentCreditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_credit', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('commited');
            $table->integer('credit_amount')->nullable();
            $table->boolean('demo')->nullable();
            $table->string('first_name', 20)->nullable();
            $table->string('last_name', 20)->nullable();
            $table->string('middle_name', 20)->nullable();
            $table->string('order_id')->nullable();
            $table->float('monthly_payment')->nullable();
            $table->float('order_amount')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('product', 20)->nullable();
            $table->string('status', 20)->nullable();
            $table->integer('term')->nullable();
            $table->integer('payment_id')->nullable();

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
        Schema::dropIfExists('payment_credit');
    }
}
