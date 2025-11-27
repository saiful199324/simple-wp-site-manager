<?php

namespace App\Jobs;

use App\Models\Site;
use App\Services\ServerManagerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class DeployWordpressSite implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Site $site;

    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    public function handle(ServerManagerService $server): void
    {
        // mark as deploying
        $this->site->update([
            'status' => 'deploying',
            'monitor_token' => $this->site->monitor_token ?: Str::random(40),
        ]);

        try {
            // call your SSH/Docker service
            $server->deploy($this->site);

            // if OK, mark as running
            $this->site->update(['status' => 'running']);
        } catch (Throwable $e) {
            // on error, mark as failed and log
            $this->site->update(['status' => 'failed']);

            Log::error('Deploy failed', [
                'site_id' => $this->site->id,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
