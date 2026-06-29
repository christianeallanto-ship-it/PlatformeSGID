<?php

namespace Database\Factories;

use App\Models\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Collection>
 */
class CollectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $routes = [
            'Akpakpa - Sainte Rita',
            'Zongo - Houénoussou',
            'Gbedjromede - Fidjrossè',
            'Cadjehoun - Gbégamey',
            'Kouhounou - Saint Michel',
            'Menontin - Agla'
        ];

        return [
            'route_name' => $this->faker->randomElement($routes),
            'driver_name' => $this->faker->name(),
            'scheduled_at' => $this->faker->dateTimeBetween('-2 days', '+2 days'),
            'status' => $this->faker->randomElement(['Planifiée', 'En cours', 'Terminée']),
        ];
    }
}
