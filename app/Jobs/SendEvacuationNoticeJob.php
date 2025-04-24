<?php

namespace App\Jobs;

use App\Actions\SendMessage;
use App\Models\CheckIn;
use App\Models\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEvacuationNoticeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Site $site)
    {
    }

    public function handle(SendMessage $action): void
    {
        $checkedInWorkers = CheckIn::where('site_id', $this->site->id)
            ->whereNull('checkout')
            ->with('worker')
            ->get();

        foreach ($checkedInWorkers as $checkin) {
            $action->handle("🚨 Evacuation Notice: Please evacuate site '{$this->site->name}' immediately. Safety first!", $checkin->worker->phone);
        }
    }
}
