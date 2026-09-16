<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinic_landing_pages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->nullable(); // who created it (user_id)

            // Identity
            $table->string('clinic_name');
            $table->string('slug')->unique();

            // Content
            $table->longText('about_clinic')->nullable();
            $table->string('logo')->nullable();
            $table->text('timings')->nullable();
            $table->text('address')->nullable();
            $table->text('services')->nullable();          // one per line
            $table->decimal('clinic_rating', 3, 1)->default(0); // 0.0 – 5.0

            // Appointment / Booking
            $table->text('booking_slots')->nullable();     // one per line
            $table->string('booking_start_time', 10)->nullable(); // e.g. 09:00
            $table->string('booking_end_time', 10)->nullable();   // e.g. 20:00
            $table->unsignedSmallInteger('slot_interval_minutes')->default(15);
            $table->boolean('is_appointment_enabled')->default(true);

            // Contact / Notifications
            $table->string('whatsapp_number', 20)->nullable();
            $table->string('notification_email')->nullable();

            // SMTP Config (clinic-level)
            $table->string('smtp_host')->nullable();
            $table->string('smtp_port', 10)->nullable();
            $table->string('smtp_username')->nullable();
            $table->string('smtp_password')->nullable();
            $table->string('smtp_encryption', 10)->nullable(); // tls / ssl / none
            $table->string('smtp_from_address')->nullable();
            $table->string('smtp_from_name')->nullable();

            // Meta
            $table->string('status')->default('active'); // active / inactive
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_landing_pages');
    }
};
