<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id'); //ID
            $table->string('name'); //Имя
            $table->string('email')->unique()->nullable(); //Email
            $table->string('phone')->unique(); //Телефон

            $table->unsignedBigInteger('category_id')->default(1); //Юрист, бухгалтер категория пользователя
            $table->float('balance')->default(0.0); //Баланс
            $table->string('email_notification')->nullable(); //Email для уведомлений
            $table->unsignedBigInteger('manager_id')->nullable(); //За каким менеджером закреплён
            $table->unsignedBigInteger('contact_id')->nullable(); //Контакт в битрикс
            $table->float('bonus')->default(0.0); //Бонусы
            $table->boolean('with_bonus')->default(false); //Оплачивать с бонусов
            $table->enum('role', [
                'ROLE_ADMIN',
                'ROLE_USER',
                'ROLE_WEBMASTER',
                'ROLE_MANAGER',
                'ROLE_ACCOUNTANT'
            ])->default('ROLE_USER'); //Роль пользователя в системе
            $table->boolean('is_registration')->default(false); //Зарегистрирован или нет (true в случае если он зашёл под sms коду)
            $table->boolean('is_delete')->default(false); //Удалён
            $table->datetime('was_online')->default(date("Y-m-d H:i:s")); //Когда был онлайн
            $table->boolean('is_demo')->default(false); //Прошёл ли упрощённую версию
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
        Schema::dropIfExists('users');
    }
}
