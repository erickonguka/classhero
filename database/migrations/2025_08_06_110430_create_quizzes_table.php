<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->integer('passing_score')->default(70);
            $table->integer('max_attempts')->default(3);
            $table->integer('time_limit')->nullable(); // in minutes
            $table->boolean('is_required')->default(true);
            $table->boolean('show_results')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};