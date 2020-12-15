<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequisitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requisites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable(); //Название компании
            $table->string('ogrn')->nullable(); //ОГРН
            $table->string('inn')->nullable(); //ИНН
            $table->string('kpp')->nullable(); //КПП
            $table->string('bik')->nullable(); //Бик
            $table->string('bank')->nullable(); //Банк
            $table->string('ksch')->nullable();// К/СЧ
            $table->string('rsch')->nullable();// Р/СЧ
            $table->string('jour_address')->nullable();//Юр. адрес
            $table->string('poste_address')->nullable();//Почтовый адрес
            $table->string('director')->nullable();//Ген директор
            
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onUpdate('restrict')
                    ->onDelete('set null');

            $table->boolean('is_delete')->default(false);
            
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
        Schema::dropIfExists('requisites');
    }
}
