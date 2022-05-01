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
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn([
                'ready',
                'favorite',
                'orig_filename',
                'filters',
                'conversions',
                'order', 
                'taken_at',
            ]);

            $table->json('exif')->nullable()->after('size');
            $table->renameColumn('size', 'filesize');
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
            $table->renameColumn('filesize', 'size');
            $table->json('filters')->nullable()->after('filesize');
            $table->json('conversions')->nullable()->after('filters');
            $table->unsignedInteger('order')->nullable()->after('tags');
            $table->timestamp('taken_at')->nullable()->after('order');
        });
    }
};