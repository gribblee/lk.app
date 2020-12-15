<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDealsFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deals_files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('ext');
            $table->unsignedBigInteger('deal_id');
            $table->foreign('deal_id')
                    ->references('id')
                    ->on('deals')
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
        Schema::dropIfExists('deals_files');
    }
}
