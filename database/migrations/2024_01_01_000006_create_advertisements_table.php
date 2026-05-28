<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['adsterra', 'google_adsense', 'custom_html', 'image_banner']);
            $table->enum('placement', ['redirect_page', 'dashboard_top', 'dashboard_side', 'homepage']);
            $table->longText('ad_code')->nullable();
            $table->string('image_url')->nullable();
            $table->string('link_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
