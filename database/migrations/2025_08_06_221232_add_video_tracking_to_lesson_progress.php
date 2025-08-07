<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->integer('video_watched_seconds')->default(0)->after('completed_at');
            $table->boolean('video_completed')->default(false)->after('video_watched_seconds');
            $table->boolean('audio_enabled')->default(false)->after('video_completed');
            $table->boolean('quiz_passed')->default(false)->after('audio_enabled');
            $table->boolean('comment_posted')->default(false)->after('quiz_passed');
        });
    }

    public function down(): void
    {
        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->dropColumn(['video_watched_seconds', 'video_completed', 'audio_enabled', 'quiz_passed', 'comment_posted']);
        });
    }
};