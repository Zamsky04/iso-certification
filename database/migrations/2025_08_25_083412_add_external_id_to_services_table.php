<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'external_id')) {
                $table->string('external_id')->nullable()->unique()->after('id');
            }
            // Pastikan metadata tersimpan sebagai JSON
            $table->json('metadata')->nullable()->change();
        });
    }
    public function down(): void {
        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'external_id')) {
                $table->dropUnique(['external_id']);
                $table->dropColumn('external_id');
            }
        });
    }
};
