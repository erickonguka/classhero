<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_banned')->default(false);
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->boolean('is_banned')->default(false);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('has_certificate')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_banned');
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn('is_banned');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('has_certificate');
        });
    }
};