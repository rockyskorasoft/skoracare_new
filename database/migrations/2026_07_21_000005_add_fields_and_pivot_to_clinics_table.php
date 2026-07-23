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
        Schema::table('clinics', function (Blueprint $table) {
            if (!Schema::hasColumn('clinics', 'email')) {
                $table->string('email')->nullable()->after('name');
            }
            if (!Schema::hasColumn('clinics', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            if (!Schema::hasColumn('clinics', 'state')) {
                $table->string('state')->nullable()->after('city');
            }
            if (!Schema::hasColumn('clinics', 'postal_code')) {
                $table->string('postal_code')->nullable()->after('state');
            }
            if (!Schema::hasColumn('clinics', 'status')) {
                $table->string('status')->default('active')->after('logo');
            }
        });

        if (!Schema::hasTable('clinic_user')) {
            Schema::create('clinic_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('clinic_id')->constrained('clinics')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['user_id', 'clinic_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinic_user');

        Schema::table('clinics', function (Blueprint $table) {
            if (Schema::hasColumn('clinics', 'email')) {
                $table->dropColumn('email');
            }
            if (Schema::hasColumn('clinics', 'city')) {
                $table->dropColumn('city');
            }
            if (Schema::hasColumn('clinics', 'state')) {
                $table->dropColumn('state');
            }
            if (Schema::hasColumn('clinics', 'postal_code')) {
                $table->dropColumn('postal_code');
            }
            if (Schema::hasColumn('clinics', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
