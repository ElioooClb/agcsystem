<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Générer une date de requested_at
        $requestedAt = $this->faker->dateTimeBetween('-1 year', 'now');

        // Générer une date de filled_at après requested_at
        $filledAt = $this->faker->dateTimeBetween($requestedAt, 'now');

        return [
            'number' => $this->generateFormattedString(),
            'filled_at' => $filledAt->format('Y-m-d'),
            'requested_at' => $requestedAt->format('Y-m-d'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    public function generateFormattedString()
    {
        $year = substr($this->faker->year(), -2);
        $month = str_pad($this->faker->numberBetween(1, 12), 2, '0', STR_PAD_LEFT);
        $randomDigits = $this->faker->numerify('####');

        return "{$year}-{$month}-{$randomDigits}";
    }
}
