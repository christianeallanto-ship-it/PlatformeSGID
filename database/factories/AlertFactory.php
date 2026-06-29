<?php

namespace Database\Factories;

use App\Models\Alert;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Alert>
 */
class AlertFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bin_id' => \App\Models\Bin::factory(),
            'message' => 'Le bac de déchets a dépassé le seuil de remplissage critique.',
            'is_resolved' => $this->faker->boolean(40), // 40% de chances d'être déjà résolu
        ];
    }
}
