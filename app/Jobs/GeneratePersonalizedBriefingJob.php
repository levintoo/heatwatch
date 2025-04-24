<?php

namespace App\Jobs;

use App\Actions\SendMessage;
use App\Models\Site;
use App\Models\Worker;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use OpenAI\Laravel\Facades\OpenAI;

class GeneratePersonalizedBriefingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Worker $worker)
    {
    }

    public function handle(SendMessage $action): void
    {
        $site = Site::first();
        $worker = $this->worker;
        $forecast = $this->getForecast();
        $environment = Cache::get('live_serial');
        $prompt = <<<PROMPT
You are a safety assistant for a construction worker named {$worker->name} at {$site->location}.
They have a BMI of {$worker->bmi}. Current room conditions: temperature is {$environment['temperature']}°C,
humidity is {$environment['humidity']}%, and heat index is {$environment['heat_index']}°C.
Today's weather forecast is: {$forecast}.

Generate a short, friendly, and safety-focused message for the worker.
If heat index is high, warn them. If BMI is high, advise them accordingly.
Include a daily safety tip. Keep it professional, warm, and under 800 characters.
PROMPT;

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini-2024-07-18',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful safety assistant for construction workers.'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        $safetyMessage = $response['choices'][0]['message']['content'];

        $action->handle($safetyMessage, '');
    }

    public function getForecast()
    {
        $cacheKey = 'weather_forecast_nairobi';

        $res = Cache::get($cacheKey);

        return Cache::remember($cacheKey, 60 * 60 * 24, function () {
            $response = Http::get('http://api.weatherapi.com/v1/forecast.json', [
                'key' => config('services.weather.api_key'),
                'q' => 'Nairobi',
            ]);

            return $response->body();
        });
    }
}
