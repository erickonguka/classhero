<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->json('completion_requirements')->nullable()->after('is_published');
            $table->boolean('require_video_completion')->default(false)->after('completion_requirements');
            $table->boolean('require_quiz_pass')->default(false)->after('require_video_completion');
            $table->boolean('require_comment')->default(false)->after('require_quiz_pass');
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn(['completion_requirements', 'require_video_completion', 'require_quiz_pass', 'require_comment']);
        });
    }
};