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
        // 1. Migrate data logic
        // We use raw DB queries to avoid Model logic interference during migration
        $users = DB::table('users')->get();
        
        foreach ($users as $user) {
            $currentRole = $user->role;
            $roleV2 = $user->role_v2 ?? null;
            
            // Determine the definitive role
            $finalRole = $currentRole;
            
            // If role_v2 is present and not empty, it takes precedence
            if (!empty($roleV2)) {
                $finalRole = strtolower($roleV2);
            }
            
            // Normalize standard roles
            $finalRole = match (strtolower($finalRole)) {
                'member' => 'user',
                'volunteer' => 'translator',
                default => strtolower($finalRole),
            };
            
            // Update if different
            if ($finalRole !== $currentRole) {
                DB::table('users')->where('id', $user->id)->update(['role' => $finalRole]);
            }
        }

        // 2. Drop the redundant column
        // Split into separate operations for better SQLite compatibility
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_v2_index'); 
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role_v2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role_v2')->nullable()->after('role');
            $table->index('role_v2');
        });
    }
};
