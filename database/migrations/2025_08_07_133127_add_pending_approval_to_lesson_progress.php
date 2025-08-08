<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPendingApprovalToLessonProgress extends Migration
{
    public function up()
    {
        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->boolean('pending_approval')->default(false)->after('is_completed');
        });
    }

    public function down()
    {
        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->dropColumn('pending_approval');
        });
    }
}
