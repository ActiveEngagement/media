<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if(Schema::hasTable('mediables')) {
            return;
        }

        Schema::create('mediables', function($table) {
			$table->increments('id');
            $table->integer('model_id')->unsigned();
            $table->foreign('model_id')->references('id')->on('media')->onDelete('cascade')->onUpdate('cascade');
            $table->morphs('mediable');
            $table->boolean('favorite')->default(false);
            $table->integer('order')->default(0);
		});
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('mediables');
    }
};