<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'language')) {
                $table->string('language', 2)->default('vi')->after('locale');
            }

            if (!Schema::hasColumn('users', 'religion')) {
                $table->string('religion')->nullable()->after('language');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'language')) {
                $table->dropColumn('language');
            }

            if (Schema::hasColumn('users', 'religion')) {
                $table->dropColumn('religion');
            }
        });
    }
};
