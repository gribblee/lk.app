<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name');
            $table->string('phone');
            $table->string('email');

            $table->unsignedBigInteger('region_id')->nullable();
            $table->foreign('region_id')
                ->references('id')
                ->on('regions')
                ->onUpdate('restrict')
                ->onDelete('set null');

            $table->unsignedBigInteger('direction_id')->nullable();
            $table->foreign('direction_id')
                ->references('id')
                ->on('directions')
                ->onUpdate('restrict')
                ->onDelete('set null');

            $table->unsignedBigInteger('bid_id')->nullable();
            $table->foreign('bid_id')
                ->references('id')
                ->on('bids')
                ->onUpdate('restrict')
                ->onDelete('set null');

            $table->unsignedBigInteger('token_id')->nullable();
            $table->foreign('token_id')
                ->references('id')
                ->on('api_token')
                ->onUpdate('restrict')
                ->onDelete('set null');

            $table->unsignedBigInteger('status_id')->nullable();
            $table->foreign('status_id')
                ->references('id')
                ->on('status')
                ->onUpdate('restrict')
                ->onDelete('set null');

            $table->json('utm')->default('[]');
            $table->json('request')->default('[]');
            $table->string('referer')->default('');
            $table->float('amount')->default(0.0);

            $table->boolean('is_insurance')->default(false);

            $table->boolean('is_view')->default(false);
            $table->boolean('is_manager_view')->default(false);
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
        Schema::dropIfExists('deals');
    }
}
