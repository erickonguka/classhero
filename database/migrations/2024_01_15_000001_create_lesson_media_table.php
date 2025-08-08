<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lesson_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->string('type'); // video, audio, image, pdf, document, youtube, link
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('url')->nullable(); // for youtube/external links
            $table->string('file_path')->nullable(); // for uploaded files
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lesson_media');
    }
};