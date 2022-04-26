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
        Schema::create('media', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned()->nullable();
            $table->foreign('parent_id')->references('id')->on('media')->onDelete('cascade')->onUpdate('cascade');
            $table->string('disk')->nullable();
            $table->string('context')->nullable();
            $table->string('title')->nullable();
            $table->string('caption')->nullable();
            $table->string('directory')->nullable();
            $table->double('filesize')->default(0);
            $table->string('filename');
            $table->string('extension')->nullable();
            $table->string('mime')->nullable();
            $table->json('exif')->nullable();
            $table->json('meta')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('media');
    }
};