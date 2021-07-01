<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebmasterReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webmaster_report', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('deal_id')->unsigned();
            $table->foreign('deal_id')
                ->references('id')
                ->on('deals')
                ->onDelete('set null');

            $table->bigInteger('api_id')->unsigned();
            $table->foreign('api_id')
                ->references('id')
                ->on('api_token')
                ->onDelete('set null');


            $table->json('utm')->nullable();

            $table->float('amount'); //Сумма которую вебмастер получил 
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
        Schema::dropIfExists('webmaster_report');
    }
}
