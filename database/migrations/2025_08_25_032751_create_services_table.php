<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('services', function (Blueprint $t) {
            $t->id();
            $t->string('external_id')->nullable()->index();
            $t->longText('title');
            $t->string('slug', 191)->unique();
            $t->string('category')->nullable()->index();
            $t->longText('description')->nullable();
            $t->text('short_description')->nullable();
            $t->string('image_url')->nullable();
            $t->string('cta_text')->nullable();
            $t->string('cta_url')->nullable();
            $t->boolean('featured')->default(false)->index();
            $t->json('metadata')->nullable();
            $t->json('benefits')->nullable();
            $t->json('requirements')->nullable();
            $t->timestamps();
            $t->fullText(['title', 'short_description', 'description'], 'services_fulltext');
        });
    }

    public function down(): void {
        Schema::dropIfExists('services');
    }
};
