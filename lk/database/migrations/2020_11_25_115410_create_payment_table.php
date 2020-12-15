<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('type');
            $table->integer('status');
            $table->string('tcv_id')->nullable();
            $table->string('card')->nullable();
            $table->unsignedBigInteger('payment_id')->nullable();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('restrict')
                ->onDelete('set null');

            $table->unsignedBigInteger('requisite_id')->nullable();
            $table->foreign('requisite_id')
                ->references('id')
                ->on('requisites')
                ->onUpdate('restrict')
                ->onDelete('set null');

            $table->unsignedBigInteger('paysum');


            $table->unsignedBigInteger('before_balance');
            $table->unsignedBigInteger('after_balance')->nullable();

            $table->boolean('is_demo')->default(false);

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
        Schema::dropIfExists('payment');
    }
}
