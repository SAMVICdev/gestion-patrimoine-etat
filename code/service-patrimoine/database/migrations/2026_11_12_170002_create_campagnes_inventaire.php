<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campagnes_inventaire', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->string('statut')->default('en_cours')->index();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->foreignId('cree_par')->constrained('users')->restrictOnDelete();
            $table->foreignId('cloture_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('date_debut');
            $table->timestamp('date_cloture')->nullable();
            $table->timestamps();
        });

        Schema::create('constats_inventaire', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campagne_id')->constrained('campagnes_inventaire')->cascadeOnDelete();
            $table->foreignId('bien_id')->constrained('biens')->restrictOnDelete();
            $table->string('resultat')->index();
            $table->text('commentaire')->nullable();
            $table->foreignId('agent_id')->constrained('users')->restrictOnDelete();
            $table->timestamp('date_constat');
            $table->timestamps();
            $table->unique(['campagne_id', 'bien_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('constats_inventaire');
        Schema::dropIfExists('campagnes_inventaire');
    }
};
