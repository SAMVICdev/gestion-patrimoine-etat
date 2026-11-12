<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('nom')->unique();
            $table->text('description')->nullable();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->string('nom')->unique();
            $table->text('description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropUnique(['nom']);
            $table->dropColumn(['nom', 'description']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['nom']);
            $table->dropColumn(['nom', 'description']);
        });
    }
};
