<?php

namespace Database\Factories;

use App\Models\Deal;
use Illuminate\Database\Eloquent\Factories\Factory;

class DealFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Deal::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'phone' => '+7 (938) 305-07-24',
            'email' => $this->faker->unique()->safeEmail,
            'direction_id' => 2,
            'region_id' => null,
            'status_id' => 5
        ];
    }
}
