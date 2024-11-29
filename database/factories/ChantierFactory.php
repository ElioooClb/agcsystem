<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use App\Models\Invoice;
use App\Models\State;
use Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chantier>
 */
class ChantierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = Faker\Factory::create();
        return [
            'title' => $faker->sentence(),
            'hours' => $faker->numberBetween(1, 100),
            'observation' => $faker->sentence(),
            'visible' => $faker->boolean(),
            'materialamount' => $faker->randomFloat(2, 10, 1000),
            'serviceamount' => $faker->randomFloat(2, 10, 1000),
            'color' => $faker->hexColor(),
            'realisation_date' => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'state' => State::all()->random()->code,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    /**
     * Define the relation to invoices.
     *
     * @param  array  $attributes
     * @return $this
     */
    public function withInvoice($attributes = [])
    {
        return $this->hasOne(Invoice::factory(), $attributes);
    }
}
