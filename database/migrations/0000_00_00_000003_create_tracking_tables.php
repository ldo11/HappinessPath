<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_journeys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('current_day')->default(1);
            $table->string('custom_focus')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'current_day']);
            $table->index('last_activity_at');
        });

        Schema::create('user_quiz_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('heart_score')->nullable();
            $table->unsignedTinyInteger('grit_score')->nullable();
            $table->unsignedTinyInteger('wisdom_score')->nullable();
            $table->string('dominant_issue')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('user_id');
        });

        Schema::create('user_numerologies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('life_path_number')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });

        Schema::create('user_daily_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('daily_task_id')->constrained('daily_tasks')->cascadeOnDelete();
            $table->text('report_content');
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('xp_awarded')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'daily_task_id']);
            $table->index(['user_id', 'completed_at']);
        });

        Schema::create('user_video_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('video_id')->constrained('videos')->cascadeOnDelete();
            $table->timestamp('claimed_at')->nullable();
            $table->unsignedInteger('xp_awarded')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'video_id']);
            $table->index(['user_id', 'claimed_at']);
        });

        Schema::create('user_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();

            $table->json('answers');
            $table->json('pillar_scores')->nullable();
            $table->integer('total_score')->default(0);
            $table->string('result_label')->nullable();

            $table->string('submission_mode')->nullable();
            $table->foreignId('consultation_thread_id')->nullable()->constrained('consultation_threads')->nullOnDelete();

            $table->timestamps();

            $table->index('user_id');
            $table->index('assessment_id');
            $table->index('consultation_thread_id');
        });

        Schema::create('assessment_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_thread_id')->constrained('consultation_threads')->cascadeOnDelete();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            $table->string('access_token', 64)->unique();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('consultation_thread_id');
            $table->index('assessment_id');
            $table->index('user_id');
            $table->index('access_token');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_assignments');
        Schema::dropIfExists('user_assessments');
        Schema::dropIfExists('user_video_logs');
        Schema::dropIfExists('user_daily_tasks');
        Schema::dropIfExists('user_numerologies');
        Schema::dropIfExists('user_quiz_results');
        Schema::dropIfExists('user_journeys');
    }
};
