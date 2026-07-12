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
        $person = NaturalPerson::factory()->create();

        return [
            'n_charge' => $this->faker->unique()->numberBetween(1, 1000),
            'charge_period' => '2026',
            'user_id' => User::factory(),
            'interesado_type' => NaturalPerson::class,
            'interesado_id' => $person->id,
            'asunto' => $this->faker->sentence(),
            'document_date' => $this->faker->date(),
        ];
    }
}
