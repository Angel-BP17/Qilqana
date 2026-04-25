<?php

namespace Database\Factories;

use App\Models\Charge;
use App\Models\NaturalPerson;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChargeFactory extends Factory
{
    protected $model = Charge::class;

    public function definition(): array
    {
        return [
            'n_charge' => $this->faker->unique()->numberBetween(1, 1000),
            'charge_period' => '2026',
            'user_id' => User::factory(),
            'tipo_interesado' => 'Persona Natural',
            'natural_person_id' => NaturalPerson::factory(),
            'asunto' => $this->faker->sentence(),
            'document_date' => $this->faker->date(),
        ];
    }
}
