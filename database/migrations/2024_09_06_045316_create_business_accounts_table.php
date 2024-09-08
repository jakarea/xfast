<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('business_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('business_name');
            $table->string('category');
            $table->string('business_title');
            $table->string('location');
            $table->string('phone1');
            $table->string('phone2')->nullable();
            $table->string('whatsapp');
            $table->string('email');
            $table->string('website')->nullable();
            $table->text('product_services');
            $table->text('business_description');
            $table->string('logo')->nullable();
            $table->string('cover_photo')->nullable();
            $table->json('company_images')->nullable();
            $table->json('company_videos')->nullable();
            $table->json('social_media_links')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_accounts');
    }
};
