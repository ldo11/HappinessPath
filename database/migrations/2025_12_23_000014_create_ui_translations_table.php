<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
    }

    public function down(): void
    {
        Schema::dropIfExists('ui_translations');
    }
};
