<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Nettoyage des tables existantes pour éviter les doublons
        \Schema::disableForeignKeyConstraints();
        \App\Models\Alert::truncate();
        \App\Models\Bin::truncate();
        \App\Models\Collection::truncate();
        \Schema::enableForeignKeyConstraints();

        // 2. Création ou récupération de l'utilisateur administrateur
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrateur',
                'password' => \Hash::make('password'),
                'role' => 'Administrateur',
                'is_active' => true,
            ]
        );
        if ($admin->role !== 'Administrateur') {
            $admin->update([
                'role' => 'Administrateur',
                'is_active' => true,
            ]);
        }

        // 3. Création de 50 Bacs fictifs
        $bins = \App\Models\Bin::factory(50)->create();

        // 3. Création automatique d'alertes pour les bacs remplis à 60% et plus
        foreach ($bins as $bin) {
            if ($bin->fill_level >= 60) {
                $statusType = ($bin->fill_level >= 80) ? 'plein' : 'presque plein';
                
                \App\Models\Alert::create([
                    'bin_id' => $bin->id,
                    'message' => "Alerte : Le bac {$bin->code} situé à {$bin->location} est {$statusType} ({$bin->fill_level}%).",
                    'is_resolved' => ($bin->fill_level < 80 && rand(0, 1)), // Plus de chances de résolution si ce n'est pas complètement plein
                ]);
            }
        }

        // 4. Création de 10 Collectes/Tournées fictives
        \App\Models\Collection::factory(10)->create();
    }
}
