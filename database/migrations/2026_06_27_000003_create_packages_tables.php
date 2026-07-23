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
            $table->text('description')->nullable();
            $table->decimal('monthly_price', 10, 2)->default(0.00);
            $table->decimal('yearly_price', 10, 2)->default(0.00);
            $table->integer('clinic_limit')->default(-1); // -1 means unlimited
            $table->integer('user_limit')->default(-1);   // -1 means unlimited
            $table->string('status')->default('active');
            $table->boolean('is_popular')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Create package_permissions pivot table
        Schema::create('package_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->timestamps();
        });

        // 3. Update users table with package_id, max_clinics, created_by
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'package_name')) {
                $table->dropColumn('package_name');
            }
            if (!Schema::hasColumn('users', 'package_id')) {
                $table->foreignId('package_id')->nullable()->constrained('packages')->onDelete('set null');
            }
            if (!Schema::hasColumn('users', 'max_clinics')) {
                $table->integer('max_clinics')->nullable()->comment('Custom clinic limit assigned by Admin, null uses package limit');
            }
            if (!Schema::hasColumn('users', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'package_id')) {
                $table->dropForeign(['package_id']);
                $table->dropColumn('package_id');
            }
            if (Schema::hasColumn('users', 'max_clinics')) {
                $table->dropColumn('max_clinics');
            }
            if (Schema::hasColumn('users', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
        });

        Schema::dropIfExists('package_permissions');
        Schema::dropIfExists('packages');
    }
};
