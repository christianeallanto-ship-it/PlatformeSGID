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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('Superviseur'); // 'Administrateur', 'Superviseur'
            $table->boolean('is_active')->default(true);
        });

        // Insertion automatique de l'Administrateur par défaut
        \DB::table('users')->insertOrIgnore([
            'name' => 'Administrateur',
            'email' => 'admin@admin.com',
            'password' => \Hash::make('password'),
            'role' => 'Administrateur',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'is_active']);
        });
    }
};
