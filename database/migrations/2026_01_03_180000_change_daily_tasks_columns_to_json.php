<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_tasks', function (Blueprint $table) {
            // Change title and description to json for translations
            // Note: In SQLite, json is text, but we declare it explicitly for Laravel checks
            $table->text('title')->change(); 
            $table->text('description')->change();
        });
    }

    public function down(): void
    {
        Schema::table('daily_tasks', function (Blueprint $table) {
            $table->string('title')->change();
            $table->text('description')->change();
        });
    }
};
