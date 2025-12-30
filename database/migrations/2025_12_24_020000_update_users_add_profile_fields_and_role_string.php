<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // SQLite: rebuild the table to safely change role enum -> string and add new columns
            DB::statement('PRAGMA foreign_keys=OFF');

            if (Schema::hasTable('users_old')) {
                Schema::drop('users_old');
            }
            if (Schema::hasTable('users_new')) {
                Schema::drop('users_new');
            }

            $rows = DB::table('users')->get();
            DB::statement('DROP INDEX IF EXISTS users_email_unique');

            Schema::create('users_new', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');

                $table->date('dob')->nullable();
                $table->string('disc_type', 1)->nullable();
                $table->string('role')->default('user');

                $table->enum('spiritual_preference', ['buddhism', 'christianity', 'secular'])->nullable();
                $table->enum('onboarding_status', ['new', 'test_skipped', 'test_completed'])->default('new');

                $table->unsignedTinyInteger('start_pain_level')->nullable();

                $table->string('city')->nullable();
                $table->string('district')->nullable();
                $table->string('country')->nullable();
                $table->boolean('geo_privacy')->default(true);

                if (!Schema::hasColumn('users_old', 'locale')) {
                    $table->string('locale')->nullable();
                } else {
                    $table->string('locale')->nullable();
                }

                $table->rememberToken();
                $table->timestamps();
                $table->softDeletes();
            });

            foreach ($rows as $row) {
                $oldRole = $row->role ?? 'member';
                $newRole = match ($oldRole) {
                    'member' => 'user',
                    default => $oldRole,
                };

                DB::table('users_new')->insert([
                    'id' => $row->id,
                    'name' => $row->name,
                    'email' => $row->email,
                    'email_verified_at' => $row->email_verified_at,
                    'password' => $row->password,
                    'dob' => $row->dob ?? null,
                    'disc_type' => $row->disc_type ?? null,
                    'role' => $newRole,
                    'spiritual_preference' => $row->spiritual_preference,
                    'onboarding_status' => $row->onboarding_status,
                    'start_pain_level' => $row->start_pain_level,
                    'city' => $row->city,
                    'district' => $row->district,
                    'country' => $row->country,
                    'geo_privacy' => $row->geo_privacy,
                    'locale' => $row->locale ?? null,
                    'remember_token' => $row->remember_token,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                    'deleted_at' => $row->deleted_at,
                ]);
            }

            Schema::drop('users');
            Schema::rename('users_new', 'users');

            DB::statement('CREATE INDEX IF NOT EXISTS users_role_index ON users (role)');
            DB::statement('CREATE INDEX IF NOT EXISTS users_spiritual_preference_index ON users (spiritual_preference)');
            DB::statement('CREATE INDEX IF NOT EXISTS users_onboarding_status_index ON users (onboarding_status)');

            DB::statement('PRAGMA foreign_keys=ON');
        } else {
            // Non-SQLite: add columns and switch role to string without requiring DBAL
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'dob')) {
                    $table->date('dob')->nullable()->after('password');
                }

                if (!Schema::hasColumn('users', 'disc_type')) {
                    $table->string('disc_type', 1)->nullable()->after('dob');
                }
            });

            if (!Schema::hasColumn('users', 'role_new')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('role_new')->nullable();
                });
            }

            DB::table('users')->update([
                'role_new' => DB::raw("CASE role
                    WHEN 'member' THEN 'user'
                    ELSE role
                END"),
            ]);
            DB::table('users')->whereNull('role_new')->update(['role_new' => 'user']);

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('role_new', 'role');
                $table->index('role');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'dob')) {
                $table->dropColumn('dob');
            }

            if (Schema::hasColumn('users', 'disc_type')) {
                $table->dropColumn('disc_type');
            }
        });
    }
};
