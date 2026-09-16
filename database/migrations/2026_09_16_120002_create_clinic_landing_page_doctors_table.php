<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinic_landing_page_doctors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('landing_page_id');
            $table->string('doctor_name');
            $table->string('email')->nullable();
            $table->string('specialization')->nullable();
            $table->string('consultation_fee')->nullable();
            $table->string('experience')->nullable();    // e.g. "5 Years"
            $table->string('photo')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('landing_page_id')
                  ->references('id')->on('clinic_landing_pages')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_landing_page_doctors');
    }
};
