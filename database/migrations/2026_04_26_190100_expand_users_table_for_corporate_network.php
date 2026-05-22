<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->default('');
            }

            if (! Schema::hasColumn('users', 'position')) {
                $table->string('position')->default('Employee');
            }

            if (! Schema::hasColumn('users', 'department')) {
                $table->string('department')->default('General');
            }

            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable();
            }

            if (! Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->default('avatars/default-avatar.svg');
            }

            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user');
            }

            if (! Schema::hasColumn('users', 'last_login')) {
                $table->timestamp('last_login')->nullable();
            }
        });

        DB::table('users')->whereNull('avatar')->update(['avatar' => 'avatars/default-avatar.svg']);
        DB::table('users')->whereNull('role')->update(['role' => 'user']);
        DB::table('users')->whereNull('position')->update(['position' => 'Employee']);
        DB::table('users')->whereNull('department')->update(['department' => 'General']);

        if (DB::table('users')->where('role', 'admin')->count() === 0) {
            DB::table('users')->orderBy('id')->limit(1)->update(['role' => 'admin']);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['last_name', 'position', 'department', 'phone', 'avatar', 'role', 'last_login'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
