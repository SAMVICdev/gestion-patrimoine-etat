<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parametres_plateforme', function (Blueprint $table): void {
            $table->id();
            $table->string('cle')->unique();
            $table->text('valeur')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Valeurs par défaut
        $defaults = [
            ['cle' => 'nom_organisme',       'valeur' => 'Direction Générale du Patrimoine de l\'État', 'description' => 'Nom officiel de l\'organisme gestionnaire'],
            ['cle' => 'pays',                'valeur' => 'Togo',               'description' => 'Pays de déploiement'],
            ['cle' => 'devise',              'valeur' => 'FCFA',               'description' => 'Devise utilisée pour les valorisations'],
            ['cle' => 'email_contact',       'valeur' => '',                   'description' => 'Adresse e-mail de contact officielle'],
            ['cle' => 'telephone_contact',   'valeur' => '',                   'description' => 'Téléphone de contact officiel'],
            ['cle' => 'adresse',             'valeur' => '',                   'description' => 'Adresse physique du siège'],
            ['cle' => 'prefixe_inventaire',  'valeur' => 'PAT',                'description' => 'Préfixe des numéros d\'inventaire auto-générés'],
            ['cle' => 'annee_exercice',      'valeur' => (string) date('Y'),   'description' => 'Année de l\'exercice budgétaire en cours'],
        ];

        foreach ($defaults as $param) {
            \Illuminate\Support\Facades\DB::table('parametres_plateforme')->insert(
                array_merge($param, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('parametres_plateforme');
    }
};
