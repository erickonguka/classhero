<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('status');
            $table->enum('status', ['draft', 'pending', 'published', 'rejected', 'archived'])->default('draft')->change();
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('rejection_reason');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->change();
        });
    }
};