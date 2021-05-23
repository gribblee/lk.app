<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdsBids extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bids', function (Blueprint $table) {
            $table->boolean('is_ads')->default(false); //Является ли это рекламной компанией
            $table->unsignedBigInteger('employee_target')->nullable(); // Сколько нужно клиентов
            $table->unsignedBigInteger('employee_count')->nullable(); // Сколько сейчас
            $table->bigInteger('total_budget')->nullable(); // Какой бюджет был выделен
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bids', function (Blueprint $table) {
            //
        });
    }
}
