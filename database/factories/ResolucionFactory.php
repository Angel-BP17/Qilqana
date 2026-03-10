<?php

namespace Database\Factories;

use App\Models\Resolucion;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResolucionFactory extends Factory
{
    protected $model = Resolucion::class;

    public function definition(): array
    {
        return [
            'rd' => $this->faker->unique()->bothify('RD-####-2026'),
            'fecha' => $this->faker->date(),
            'periodo' => '2026',
            'asunto' => $this->faker->sentence(),
            'nombres_apellidos' => $this->faker->name(),
            'dni' => $this->faker->numerify('########'),
        ];
    }
}
