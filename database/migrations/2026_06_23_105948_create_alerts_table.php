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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bin_id')->constrained()->cascadeOnDelete(); // Liaison avec la table des bacs (bins)
            $table->string('message'); // Description de l'alerte (ex: Bac plein)
            $table->boolean('is_resolved')->default(false); // Statut de résolution
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
