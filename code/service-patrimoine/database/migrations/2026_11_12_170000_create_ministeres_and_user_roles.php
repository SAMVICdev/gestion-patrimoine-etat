<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ministeres', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();
            $table->string('code', 30)->unique()->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->foreignId('ministere_id')->nullable()->constrained('ministeres')->restrictOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('gestionnaire')->index();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('service_id');
            $table->dropIndex(['role']);
            $table->dropColumn('role');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ministere_id');
        });

        Schema::dropIfExists('ministeres');
    }
};
