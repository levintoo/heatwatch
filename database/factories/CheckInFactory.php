<?php

namespace Database\Factories;

use App\Models\CheckIn;
use App\Models\Site;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CheckInFactory extends Factory
{
    protected $model = CheckIn::class;

    public function definition(): array
    {
        return [
            'checkin' => Carbon::now(),
            'checkout' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'worker_id' => Worker::factory(),
            'site_id' => Site::factory(),
        ];
    }
}
