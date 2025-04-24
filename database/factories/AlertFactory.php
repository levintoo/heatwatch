<?php

namespace Database\Factories;

use App\Models\Alert;
use App\Models\Site;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AlertFactory extends Factory
{
    protected $model = Alert::class;

    public function definition(): array
    {
        return [
            'severity' => $this->faker->word(),
            'message' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'worker_id' => Worker::factory(),
            'site_id' => Site::factory(),
            'user_id' => User::factory(),
        ];
    }
}
