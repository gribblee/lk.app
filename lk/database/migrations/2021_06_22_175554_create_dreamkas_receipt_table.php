<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDreamkasReceiptTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dreamkas_receipt', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('receiptId')->default('');
            $table->string('amount')->default('');
            $table->string('type')->default('');
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->string('status')->default('');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('restrict')
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
        Schema::dropIfExists('dreamkas_receipt');
    }
}
