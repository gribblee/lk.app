<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_issues', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('title')->default('');
            $table->text('description')->default('');

            $table->integer('priceFrom')->default(0); //Цена от
            $table->integer('priceTo')->default(0); //Цена до

            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')
                ->references('id')
                ->on('company')
                ->onUpdate('RESTRICT')
                ->onDelete('SET NULL');

            $table->unsignedBigInteger('direction_id')->nullable();
            $table->foreign('direction_id')
                ->references('id')
                ->on('directions')
                ->onUpdate('RESTRICT')
                ->onDelete('SET NULL');

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
        Schema::dropIfExists('company_issues');
    }
}
