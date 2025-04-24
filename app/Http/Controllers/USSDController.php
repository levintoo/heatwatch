<?php

namespace App\Http\Controllers;

use App\Models\CheckIn;
use App\Models\Report;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Str;

class USSDController extends Controller
{
    public function __invoke(Request $request)
    {
        $sessionId = $request->input('sessionId');
        $serviceCode = $request->input('serviceCode');
        $phoneNumber = $request->input('phoneNumber');
        $text = $request->input('text');

        $response = '';

        if ($text == '') {
            $response = "CON Hello, what would you like to do? \n";
            $response .= "1. Check in for work \n";
            $response .= "2. Check out in for work \n";
            $response .= "3. Report an Incident \n";
            $response .= "4. Receive personalised safety briefing \n";
            $response .= '5. Site Safety Tips';
        }
        elseif ($text == '1') {
            $response = "CON Please input your worker ID \n";
        }
        elseif (Str::startsWith($text, '1*')) {
            $parts = explode('*', $text);
            $workerId = $parts[1] ?? null;
            try {
                $worker = Worker::findOrFail($workerId);
                CheckIn::create([
                    'worker_id' => $workerId,
                    'checkin' => now(),
                    'site_id' => 1,
                ]);
            } catch (\Throwable $throwable) {
                $response = "END Invalid worker ID \n";
            }
            $response = "END Welcome $worker->name, You are signed in successfully \n";
        }
        elseif ($text == '2') {
            $response = "CON Please input your worker ID \n";
        }
        elseif (Str::startsWith($text, '2*')) {
            $parts = explode('*', $text);
            $workerId = $parts[1] ?? null;
            try {
                $worker = Worker::findOrFail($workerId);
                $checkin = CheckIn::query()->where('worker_id', $worker->id)->latest()->firstOrFail();
                $checkin->update([
                   'checkout' => now(),
                ]);
            } catch (\Throwable $throwable) {
                $response = "END Invalid worker ID \n";
            }
            $response = "END See you soon $worker->name, You are signed out successfully \n";
        }
        elseif ($text == '3') {
            $response = "CON Please input the details of this anonymous report \n";
        }
        elseif (Str::startsWith($text, '3*')) {
            $parts = explode('*', $text);
            $report = $parts[1] ?? null;
            Report::create([
                'message' => $report,
                'reported_at' => now(),
            ]);
            $response = "END Thank for you for making this report \n";
        }
        elseif ($text == '4') {
            $response = "CON Please input your worker ID \n";
        }
        elseif (Str::startsWith($text, '4*')) {
            $workerId = explode('*', $text);
            $worker = Worker::findOrFail($workerId);
            $response = "END Thank for you for making this report \n";
        }
        elseif ($text == '5') {
            $safetyTips = [
                "Wear Your PPE (Personal Protective Equipment): Always wear your hard hat, safety boots, gloves, reflective vest, and eye protection—no exceptions.",
                "Stay Hydrated and Take Breaks: Especially in hot conditions, drink water regularly and rest in shaded areas to avoid heat stress.",
                "Report Hazards Immediately: If you see unsafe conditions, exposed wires, or damaged equipment, report them right away to prevent accidents.",
                "Use Tools and Machines Properly: Only operate equipment you’ve been trained on. Follow manufacturer and site guidelines strictly.",
                "Be Aware of Your Surroundings: Watch for moving machinery, overhead work, and falling objects. Always keep a safe distance and stay alert.",
            ];

            $randomTip = Arr::random($safetyTips);

            $response = "END $randomTip \n";
        }
        else {
            $response = "CON Invalid choice \n";
        }

        return response($response, 200)
            ->header('Content-Type', 'text/plain');
    }
}
