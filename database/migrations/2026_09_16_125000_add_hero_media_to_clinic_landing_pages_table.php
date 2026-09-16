<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinic_landing_pages', function (Blueprint $table) {
            $table->string('hero_media_type')->nullable()->default('color')->after('logo'); // 'image', 'video', or 'color'
            $table->string('hero_media')->nullable()->after('hero_media_type'); // file path
            $table->string('hero_overlay_opacity')->nullable()->default('0.6')->after('hero_media'); // e.g. 0.3 to 0.9
        });
    }

    public function down(): void
    {
        Schema::table('clinic_landing_pages', function (Blueprint $table) {
            $table->dropColumn(['hero_media_type', 'hero_media', 'hero_overlay_opacity']);
        });
    }
};
