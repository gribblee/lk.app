<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bids', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->json('regions')->default('[]');
            // $region_id = DB::connection()->getQueryGrammar()->wrap('regions');
            // $region_id = 'CAST(' . $region_id . ' AS INT)';
            // $table->computed('region_id', $region_id)->persisted();
            // $table->foreign('region_id')->references('id')->on('regions');

            $table->unsignedBigInteger('direction_id')->nullable();
            $table->foreign('direction_id')
                ->references('id')
                ->on('directions')
                ->onUpdate('RESTRICT')
                ->onDelete('SET NULL');
                
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onUpdate('RESTRICT')
                ->onDelete('SET NULL');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->float('consumption'); //Ставка
            $table->boolean('is_launch')->default(false);
            $table->boolean('is_notification')->default(false);
            $table->boolean('is_delete')->default(false);
            $table->integer('daily_limit')->default(0);
            $table->unsignedBigInteger('insurance')->default(0);

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
        Schema::dropIfExists('bids');
    }
}
