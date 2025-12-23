<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('solutions', function (Blueprint $table) {
            $table->string('locale', 10)->default('en')->after('pillar_tag');
            $table->foreign('locale')->references('code')->on('languages')->onDelete('cascade');
            $table->index('locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solutions', function (Blueprint $table) {
            $table->dropForeign(['locale']);
            $table->dropIndex(['locale']);
            $table->dropColumn('locale');
        });
    }
};
