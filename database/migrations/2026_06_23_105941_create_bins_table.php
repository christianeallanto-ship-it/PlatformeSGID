<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bins', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Code unique du bac (ex: B001)
            $table->string('location'); // Adresse ou description du lieu (ex: Akpakpa Stade)
            $table->decimal('latitude', 10, 8)->nullable(); // Latitude pour la géolocalisation
            $table->decimal('longitude', 11, 8)->nullable(); // Longitude pour la géolocalisation
            $table->integer('fill_level')->default(0); // Niveau de remplissage en pourcentage (0-100)
            $table->string('type')->default('Général'); // Type de déchet (Plastique, Organique, etc.)
            $table->string('status')->default('Normal'); // Statut du bac (Normal, Presque plein, Plein)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bins');
    }
};
