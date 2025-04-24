<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FakeSerialData extends Command
{
    protected $signature = 'serial:fake';

    protected $description = 'Fake Read serial data from Arduino';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->info('Starting fake sensor data stream...');

        while (true) {
            $humidity = round(mt_rand(3000, 8000) / 100, 2);       // e.g., 63.00
            $temperature = round(mt_rand(2000, 3500) / 100, 2);    // e.g., 25.60

            $heatIndex = round($temperature + ($humidity / 100) * 0.5, 2);

            $payload = json_encode([
                'humidity' => $humidity,
                'temperature' => $temperature,
                'heat_index' => $heatIndex,
            ]);

            $this->line($payload);

            sleep(5); // Delay 5 seconds
        }
    }
}
