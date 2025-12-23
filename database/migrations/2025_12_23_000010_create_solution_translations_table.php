<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
    }

    public function down(): void
    {
        Schema::dropIfExists('solution_translations');
    }
};
