<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBidRegionsRateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bid_regions_rate', function (Blueprint $table) {
            $table->bigInteger('amount')->default(1080);

            $table->unsignedBigInteger('bid_id')->nullable();
            $table->foreign('bid_id')
                ->references('id')
                ->on('bids')
                ->onUpdate('RESTRICT')
                ->onDelete('SET NULL');

            $table->unsignedBigInteger('region_id')->nullable();
            $table->foreign('region_id')
                ->references('id')
                ->on('regions')
                ->onUpdate('RESTRICT')
                ->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bid_regions_rate');
    }
}
