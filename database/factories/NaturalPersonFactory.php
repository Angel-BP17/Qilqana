<?php

namespace Database\Factories;

use App\Models\NaturalPerson;
use Illuminate\Database\Eloquent\Factories\Factory;

class NaturalPersonFactory extends Factory
{
    protected $model = NaturalPerson::class;

    public function definition(): array
    {
        return [
            'dni' => $this->faker->unique()->numerify('########'),
            'nombres' => $this->faker->firstName(),
            'apellido_paterno' => $this->faker->lastName(),
            'apellido_materno' => $this->faker->lastName(),
        ];
    }
}
