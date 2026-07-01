<?php

namespace Database\Factories;

use App\Models\Bin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bin>
 */
class BinFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fillLevel = $this->faker->numberBetween(0, 100);
        
        $status = 'Normal';
        if ($fillLevel >= 80) {
            $status = 'Plein';
        } elseif ($fillLevel >= 60) {
            $status = 'Presque plein';
        }

        // Liste de quartiers ou rues réelles pour faire réaliste
        $locations = [
            'Akpakpa Stade', 'Zongo Cotonou', 'Sainte Rita', 'Houénoussou', 
            'Gbedjromede', 'Gbégamey', 'Fidjrossè', 'Cadjehoun', 'Kouhounou', 
            'Saint Michel', 'Jonquet', 'Menontin', 'Agla', 'Gbégamé Gare'
        ];

        return [
            'code' => 'B' . $this->faker->unique()->numberBetween(100, 999),
            'location' => $this->faker->randomElement($locations) . ' - ' . $this->faker->streetAddress(),
            // Coordonnées géographiques centrées autour de la zone du mockup pour correspondre à la carte
            'latitude' => $this->faker->latitude(6.345000, 6.395000),
            'longitude' => $this->faker->longitude(2.380000, 2.470000),
            'fill_level' => $fillLevel,
            'temperature' => $this->faker->randomFloat(2, 20.0, 45.0),
            'air_quality' => $this->faker->numberBetween(300, 1200),
            'type' => $this->faker->randomElement(['Organique', 'Plastique', 'Verre', 'Papier', 'Métal']),
            'status' => $status,
        ];
    }
}
