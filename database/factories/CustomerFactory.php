<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'account_number' => $this->faker->unique()->randomNumber(8),
            'pin' => $this->faker->randomNumber(4),
            'account_balance' => $this->faker->numberBetween(50, 5000),
            'overdraft_available' => $this->faker->randomElement([0, 100, 200, 500]),
        ];
    }

    public function noCash()
    {
        return $this->state(function (array $attributes) {
            return [
                'account_balance' => 0,
            ];
        });
    }

    public function noOverdraft()
    {
        return $this->state(function (array $attributes) {
            return [
                'overdraft_available' => 0,
            ];
        });
    }

    public function overdrawn()
    {
        return $this->state(function (array $attributes) {
            return [
                'overdraft_available' => 50,
                'account_balance' => -25,
            ];
        });
    }

    public function fullyOverdrawn()
    {
        return $this->state(function (array $attributes) {
            return [
                'overdraft_available' => 50,
                'account_balance' => -50,
            ];
        });
    }
}
