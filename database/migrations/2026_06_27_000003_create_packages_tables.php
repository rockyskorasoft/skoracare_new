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
        // 1. Create packages table
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('clinic_limit')->default(-1); // -1 means unlimited
            $table->integer('user_limit')->default(-1);   // -1 means unlimited
            $table->timestamps();
        });

        // 2. Create package_permissions pivot table
        Schema::create('package_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->timestamps();
        });

        // 3. Alter users table to use package_id and created_by
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'package_name')) {
                $table->dropColumn('package_name');
            }
            $table->foreignId('package_id')->nullable()->constrained('packages')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->dropColumn('package_id');
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
            $table->string('package_name')->nullable();
        });

        Schema::dropIfExists('package_permissions');
        Schema::dropIfExists('packages');
    }
};
