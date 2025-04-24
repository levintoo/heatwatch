<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Cache;
use function Laravel\Prompts\clear;
use function Laravel\Prompts\error;
use function Laravel\Prompts\note;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\table;

class ReadSerialData extends Command
{
    protected $signature = 'serial:read {--debug : Output extra debug information}';

    protected $description = 'Read serial data from Arduino';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $port = '/dev/ttyUSB0';  // Adjust to your serial port (COM3 on Windows)
        $baud = 9600;

        // Set serial port settings
        exec("stty -F $port $baud");

        // Open the serial port
        $handle = fopen($port, 'r');
        if (! $handle) {
            $this->error('Unable to open serial port');

            return;
        }

        while (true) {
            $line = trim(fgets($handle));
            $data = json_decode($line, true);

            spin(
                callback: fn () => sleep(3),
            );

            if ($data !== null) {
                if (! $this->option('debug')) {
                    clear();
                }

                note(now()->format('D, d M Y H:i:s T'));

                $humidity = $data['humidity'];
                $temperature = $data['temperature'];
                $heat_index = $data['heat_index'];

                table(
                    headers: ['Humidity %', 'Temperature °C', 'Heat Index °C'],
                    rows: [[$humidity, $temperature, $heat_index]]
                );

                Cache::remember('live_serial', 60 * 60, function () use ($humidity, $temperature, $heat_index) {
                    return [
                        'humidity' => $humidity,
                        'temperature' => $temperature,
                        'heat_index' => $heat_index,
                    ];
                });
            } else {
                error('Failed to decode JSON.');
            }

            usleep(500000); // 0.5 second delay between readings
        }
    }
}
