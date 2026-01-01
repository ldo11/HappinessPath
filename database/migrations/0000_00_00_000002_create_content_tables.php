<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->string('code', 10)->primary();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ui_translations', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->string('locale', 10);
            $table->longText('value');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['key', 'locale']);
            $table->index('locale');

            $table->foreign('locale')->references('code')->on('languages')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::create('language_lines', function (Blueprint $table) {
            $table->id();
            $table->string('group');
            $table->string('key');
            $table->json('text');
            $table->timestamps();

            $table->unique(['group', 'key']);
            $table->index('group');
        });

        Schema::create('life_pillars', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->string('slug')->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('solutions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['video', 'article']);
            $table->text('url')->nullable();
            $table->string('author_name')->nullable();
            $table->enum('pillar_tag', ['heart', 'grit', 'wisdom'])->index();
            $table->string('locale', 10)->default('en');
            $table->timestamps();
            $table->softDeletes();

            $table->index('type');
            $table->index('locale');
            $table->foreign('locale')->references('code')->on('languages')->cascadeOnDelete();
        });

        Schema::create('solution_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solution_id')->constrained('solutions')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('title');
            $table->longText('content')->nullable();
            $table->boolean('is_auto_generated')->default(false);
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ai_provider')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['solution_id', 'locale']);
            $table->index(['locale', 'is_auto_generated']);
            $table->foreign('locale')->references('code')->on('languages')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::create('daily_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('day_number');
            $table->json('content')->nullable();
            $table->string('pillar_tag')->nullable();
            $table->string('difficulty')->default('easy');
            $table->unsignedTinyInteger('difficulty_level_int')->default(1);
            $table->unsignedInteger('duration')->default(15);

            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('type')->default('mindfulness');
            $table->integer('estimated_minutes')->default(10);
            $table->foreignId('solution_id')->nullable()->constrained('solutions')->nullOnDelete();
            $table->json('instructions')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['day_number', 'solution_id']);
            $table->index(['day_number', 'status']);
            $table->index('type');
            $table->index('difficulty');
            $table->index('pillar_tag');
        });

        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('description');
            $table->json('score_ranges')->nullable();
            $table->enum('status', ['created', 'translated', 'reviewed', 'active', 'special'])->default('created');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index('status');
            $table->index('created_by');
        });

        Schema::table('consultation_threads', function (Blueprint $table) {
            $table->foreign('related_assessment_id')
                ->references('id')
                ->on('assessments')
                ->nullOnDelete();
        });

        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->json('content');
            $table->enum('type', ['single_choice', 'multi_choice'])->default('single_choice');

            $table->enum('pillar_group', ['heart', 'grit', 'wisdom'])->nullable();
            $table->boolean('is_negative')->default(false);
            $table->json('related_pain_id')->nullable();

            $table->enum('pillar_group_new', ['body', 'mind', 'wisdom'])->nullable();
            $table->boolean('is_reversed')->default(false);
            $table->string('related_pain_point_key')->nullable();

            $table->integer('order');
            $table->timestamps();

            $table->index('assessment_id');
            $table->index('type');
            $table->index('order');
        });

        Schema::create('assessment_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('assessment_questions')->cascadeOnDelete();
            $table->json('content');
            $table->integer('score');
            $table->timestamps();

            $table->index('question_id');
            $table->index('score');
        });

        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('url');
            $table->enum('category', ['body', 'mind', 'wisdom']);
            $table->string('language', 2)->default('vi');
            $table->enum('pillar_tag', ['body', 'mind', 'wisdom'])->nullable();
            $table->enum('source_tag', ['buddhism', 'christianity', 'science'])->default('science');
            $table->json('pillar_tags')->nullable();
            $table->json('source_tags')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('xp_reward')->default(50);
            $table->timestamps();

            $table->index('category');
        });

        Schema::create('daily_missions', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('description')->nullable();
            $table->unsignedInteger('points')->default(0);
            $table->boolean('is_body')->default(false);
            $table->boolean('is_mind')->default(false);
            $table->boolean('is_wisdom')->default(false);
            $table->foreignId('created_by_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index('created_by_id');
        });

        Schema::create('volunteer_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('reason')->nullable();
            $table->json('skills')->nullable();
            $table->enum('status', ['pending', 'approved'])->default('pending');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
        });

        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('giver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('receiver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->text('message')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['giver_id', 'created_at']);
            $table->index(['receiver_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('consultation_threads', function (Blueprint $table) {
            $table->dropForeign(['related_assessment_id']);
        });

        Schema::dropIfExists('donations');
        Schema::dropIfExists('volunteer_applications');
        Schema::dropIfExists('daily_missions');
        Schema::dropIfExists('videos');
        Schema::dropIfExists('assessment_options');
        Schema::dropIfExists('assessment_questions');
        Schema::dropIfExists('assessments');
        Schema::dropIfExists('daily_tasks');
        Schema::dropIfExists('solution_translations');
        Schema::dropIfExists('solutions');
        Schema::dropIfExists('life_pillars');
        Schema::dropIfExists('language_lines');
        Schema::dropIfExists('ui_translations');
        Schema::dropIfExists('languages');
    }
};
