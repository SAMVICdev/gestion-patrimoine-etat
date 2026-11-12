<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transferts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bien_id')->constrained('biens')->restrictOnDelete();
            $table->foreignId('service_origine_id')->constrained('services')->restrictOnDelete();
            $table->foreignId('service_destination_id')->constrained('services')->restrictOnDelete();
            $table->string('localisation_origine');
            $table->string('localisation_destination');
            $table->text('motif');
            $table->string('justificatif')->nullable();
            $table->string('statut')->default('en_attente')->index();
            $table->foreignId('demande_par')->constrained('users')->restrictOnDelete();
            $table->foreignId('decision_par')->nullable()->constrained('users')->nullOnDelete();
            $table->text('commentaire_decision')->nullable();
            $table->timestamp('date_demande');
            $table->timestamp('date_decision')->nullable();
            $table->date('date_effet')->nullable();
            $table->timestamps();
            $table->index(['bien_id', 'statut']);
        });

        Schema::create('historique_etats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bien_id')->constrained('biens')->restrictOnDelete();
            $table->string('etat_origine');
            $table->string('etat_destination');
            $table->text('commentaire')->nullable();
            $table->string('justificatif')->nullable();
            $table->foreignId('modifie_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('date_modification');
            $table->timestamps();
            $table->index(['bien_id', 'date_modification']);
        });

        Schema::create('journal_audit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('type_objet');
            $table->unsignedBigInteger('objet_id')->nullable();
            $table->json('avant')->nullable();
            $table->json('apres')->nullable();
            $table->string('adresse_ip', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['type_objet', 'objet_id']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_audit');
        Schema::dropIfExists('historique_etats');
        Schema::dropIfExists('transferts');
    }
};
