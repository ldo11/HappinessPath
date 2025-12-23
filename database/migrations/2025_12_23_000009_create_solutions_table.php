<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solutions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['video', 'article']);
            $table->text('url')->nullable();
            $table->string('author_name')->nullable();
            $table->enum('pillar_tag', ['heart', 'grit', 'wisdom'])->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solutions');
    }
};
