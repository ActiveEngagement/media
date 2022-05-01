<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('mediables', function (Blueprint $table) {
            $table->boolean('favorite')->default(false)->after('mediable_type');
            $table->integer('order')->default(0)->after('favorite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('mediables', function (Blueprint $table) {
            $table->dropColumn('favorite');
            $table->dropColumn('order');
        });
    }
};