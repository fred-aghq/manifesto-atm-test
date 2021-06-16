<?php

namespace Database\Factories;

use App\Models\Machine;
use Illuminate\Database\Eloquent\Factories\Factory;

class MachineFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Machine::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'total_cash' => 8000,
        ];
    }

    public function empty()
    {
        return $this->state(function(array $attributes) {
            return ['total_cash' => 0];
        });
    }
}
