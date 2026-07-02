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

        // 3. Définition des villes du Bénin avec leurs coordonnées et quartiers typiques
        $villes = [
            'Cotonou'       => [
                'lat' => 6.3703,  'lng' => 2.4308,
                'neighborhoods' => ['Akpakpa', 'Cadjehoun', 'Gbégamey', 'Fidjrossè', 'Zongo', 'Saint Michel']
            ],
            'Porto-Novo'    => [
                'lat' => 6.4969,  'lng' => 2.6283,
                'neighborhoods' => ['Tokpota', 'Ouando', 'Agbokou', 'Sadognon', 'Foun-Foun']
            ],
            'Parakou'       => [
                'lat' => 9.3370,  'lng' => 2.6277,
                'neighborhoods' => ['Albarika', 'Banikanni', 'Ladji Farani', 'Tourou', 'Zongo-Zenon']
            ],
            'Abomey-Calavi' => [
                'lat' => 6.4490,  'lng' => 2.3554,
                'neighborhoods' => ['Calavi Kpota', 'Bidossessi', 'Zogbadjè', 'Tankpè', 'Togoudo', 'Arconville']
            ],
            'Bohicon'       => [
                'lat' => 7.1781,  'lng' => 2.0717,
                'neighborhoods' => ['Agbanwémé', 'Sogba', 'Gnidjazoun', 'Zakpo', 'Lissèzoun']
            ],
            'Natitingou'    => [
                'lat' => 10.3103, 'lng' => 1.3786,
                'neighborhoods' => ['Kantaborifa', 'Kouwanwankou', 'Yetapo', 'Berecingou']
            ],
            'Ouidah'        => [
                'lat' => 6.3612,  'lng' => 2.0854,
                'neighborhoods' => ['Fonsamé', 'Gbécon', 'Sogbadji', 'Zomaï', 'Oualihon']
            ],
            'Lokossa'       => [
                'lat' => 6.6384,  'lng' => 1.7173,
                'neighborhoods' => ['Agame', 'Glo', 'Lokossa Centre', 'Zongo-Lokossa']
            ],
            'Djougou'       => [
                'lat' => 9.7097,  'lng' => 1.6660,
                'neighborhoods' => ['Kilir', 'Madina', 'Taïfa', 'Zongo-Djougou', 'Sassirou']
            ],
            'Kandi'         => [
                'lat' => 11.1344, 'lng' => 2.9389,
                'neighborhoods' => ['Kandi Fo', 'Bensékou', 'Madina Kandi', 'Zongo-Kandi']
            ],
        ];

        $binCounter = 101;
        $allBins = [];

        // 4. Création de 5 bacs réalistes par ville (50 au total)
        foreach ($villes as $villeName => $data) {
            for ($i = 0; $i < 5; $i++) {
                $fillLevel = rand(10, 95);
                $status = 'Normal';
                if ($fillLevel >= 80) {
                    $status = 'Plein';
                } elseif ($fillLevel >= 60) {
                    $status = 'Presque plein';
                }

                $neighborhood = $data['neighborhoods'][array_rand($data['neighborhoods'])];
                // Offset aléatoire d'environ +/- 4 km autour du centre de la ville
                $latOffset = (rand(-25000, 25000) / 1000000);
                $lngOffset = (rand(-25000, 25000) / 1000000);

                $bin = \App\Models\Bin::create([
                    'code' => 'B' . $binCounter++,
                    'location' => $neighborhood . ', ' . $villeName,
                    'latitude' => $data['lat'] + $latOffset,
                    'longitude' => $data['lng'] + $lngOffset,
                    'fill_level' => $fillLevel,
                    'temperature' => rand(200, 450) / 10,
                    'air_quality' => rand(300, 1200),
                    'type' => ['Organique', 'Plastique', 'Verre', 'Papier', 'Métal'][rand(0, 4)],
                    'status' => $status,
                    'is_active' => true,
                ]);
                $allBins[] = $bin;
            }
        }

        // 5. Création automatique d'alertes pour les bacs remplis à 60% et plus
        foreach ($allBins as $bin) {
            if ($bin->fill_level >= 60) {
                $statusType = ($bin->fill_level >= 80) ? 'plein' : 'presque plein';
                
                \App\Models\Alert::create([
                    'bin_id' => $bin->id,
                    'message' => "Alerte : Le bac {$bin->code} situé à {$bin->location} est {$statusType} ({$bin->fill_level}%).",
                    'is_resolved' => ($bin->fill_level < 80 && rand(0, 1)),
                ]);
            }
        }

        // 6. Création de Collectes/Tournées fictives associées à chaque ville
        $drivers = ['Amadou Diallo', 'Koffi Mensah', 'Sékou Touré', 'Moussa Traoré', 'Jean Soglo', 'Pascal Kérékou', 'Hubert Maga', 'Mathieu Chabi', 'Thomas Gnonlonfoun', 'Marc Sogadji'];
        $j = 0;
        foreach ($villes as $villeName => $data) {
            $neighborhood1 = $data['neighborhoods'][0];
            $neighborhood2 = $data['neighborhoods'][1] ?? $data['neighborhoods'][0];
            
            \App\Models\Collection::create([
                'route_name' => "Route {$villeName} ({$neighborhood1} - {$neighborhood2})",
                'driver_name' => $drivers[$j % count($drivers)],
                'scheduled_at' => now()->addHours(rand(-48, 48)),
                'status' => ['Planifiée', 'En cours', 'Terminée'][rand(0, 2)],
            ]);
            $j++;
        }
    }
}
