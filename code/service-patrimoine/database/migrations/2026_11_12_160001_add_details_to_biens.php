<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biens', function (Blueprint $table) {
            $table->string('numero_inventaire')->unique();
            $table->string('designation');
            $table->text('description')->nullable();
            $table->date('date_acquisition')->nullable();
            $table->decimal('valeur_acquisition', 15, 2)->nullable();
            $table->string('source_financement')->nullable();
            $table->foreignId('categorie_id')->constrained('categories')->restrictOnDelete();
            $table->foreignId('service_id')->constrained('services')->restrictOnDelete();
            $table->string('localisation');
            $table->string('responsable')->nullable();
            $table->string('etat')->default('bon_etat')->index();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('biens', function (Blueprint $table) {
            $table->dropConstrainedForeignId('service_id');
            $table->dropConstrainedForeignId('categorie_id');
            $table->dropUnique(['numero_inventaire']);
            $table->dropIndex(['etat']);
            $table->dropSoftDeletes();
            $table->dropColumn([
                'numero_inventaire',
                'designation',
                'description',
                'date_acquisition',
                'valeur_acquisition',
                'source_financement',
                'localisation',
                'responsable',
                'etat',
            ]);
        });
    }
};
