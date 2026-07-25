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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'max_clinics')) {
                $table->integer('max_clinics')->nullable()->after('package_id')->comment('Custom clinic limit assigned by Admin, null uses package limit');
            }
            if (!Schema::hasColumn('users', 'max_users')) {
                $table->integer('max_users')->nullable()->after('max_clinics')->comment('Custom user/staff limit assigned by Admin, null uses package limit');
            }
            if (!Schema::hasColumn('users', 'package_expires_at')) {
                $table->timestamp('package_expires_at')->nullable()->after('max_users')->comment('Expiration date/time of doctor subscription package');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'max_clinics')) {
                $table->dropColumn('max_clinics');
            }
            if (Schema::hasColumn('users', 'max_users')) {
                $table->dropColumn('max_users');
            }
            if (Schema::hasColumn('users', 'package_expires_at')) {
                $table->dropColumn('package_expires_at');
            }
        });
    }
};
