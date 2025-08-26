<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('services', function (Blueprint $t) {
        $t->id(); // bigint
        $t->string('ext_id')->nullable()->index();          // id dari JSON (mis. iso-kan-001)
        $t->string('title');                                // judul
        $t->string('slug')->unique();                       // untuk SEO
        $t->string('category')->nullable()->index();        // kategori
        $t->text('description')->nullable();                // deskripsi panjang
        $t->text('short_description')->nullable();          // deskripsi singkat
        $t->string('image_url')->nullable();
        $t->string('cta_text')->nullable();
        $t->string('cta_url')->nullable();
        $t->boolean('featured')->default(false)->index();
        $t->json('metadata')->nullable();                   // { "nama-akreditasi": ..., "jenis-iso": ... }
        $t->json('benefits')->nullable();                   // []
        $t->json('requirements')->nullable();               // []
        $t->timestamps();
        // Optional: $t->fullText(['title','description']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('services');
    }
};
