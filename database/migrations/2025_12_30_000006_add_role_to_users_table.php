<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role_v2')) {
                $table->string('role_v2')->nullable()->after('role');
                $table->index('role_v2');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role_v2')) {
                $table->dropIndex(['role_v2']);
                $table->dropColumn('role_v2');
            }
        });
    }
};
