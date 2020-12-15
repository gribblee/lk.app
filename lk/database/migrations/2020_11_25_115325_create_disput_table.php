<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisputTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('disput', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name');
            $table->text('description');

            $table->unsignedBigInteger('deal_id')->unique();
            $table->foreign('deal_id')
                ->references('id')
                ->on('deals')
                ->onUpdate('restrict')
                ->onDelete('set null');

            $table->unsignedBigInteger('disput_type_id');
            $table->foreign('disput_type_id')
                ->references('id')
                ->on('disput_type')
                ->onUpdate('restrict')
                ->onDelete('set null');

            $table->integer('status')->default(1000); //1000 - Не определено. 1010 - Закрыт в пользу сервиса. 1020 - Закрыт в пользу клиента

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
        Schema::dropIfExists('disput');
    }
}
