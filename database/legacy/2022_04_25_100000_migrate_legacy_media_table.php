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
        dd('legacy');
        
        Schema::table('media', function (Blueprint $table) {
            $table->removeColumn('ready');
            $table->removeColumn('favorite');
            $table->removeColumn('orig_filename');
            $table->renameColumn('size', 'filesize');
            $table->removeColumn('filters');
            $table->removeColumn('conversions');
            $table->removeColumn('order');
            $table->removeColumn('taken_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('media', function (Blueprint $table) {
            $table->boolean('ready')->default(false)->after('parent_id');
            $table->boolean('favorite')->default(false)->after('ready');
            $table->string('orig_filename')->nullable()->after('filename');
            $table->renameColumn('filesize', 'size')->default(0);
            $table->json('filters')->nullable()->after('filesize');
            $table->json('conversions')->nullable()->after('filters');
            $table->unsignedInteger('order')->nullable()->after('tags');
            $table->timestamp('taken_at')->nullable()->after('order');
        });
    }
};