<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('restrict')
                ->onDelete('set null');

            $table->string('name', 50);
            $table->string('description', 1000);
            $table->datetime('was_create')
                ->default(date("Y-m-d H:i:s"));
            $table->integer('number_of_persons')
                ->unsigned();
            $table->string('address');
            $table->json('activity')
                ->default('[]');
            $table->integer('level')
                ->unsigned()
                ->default(0.0);

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
        Schema::dropIfExists('groups');
    }
}
