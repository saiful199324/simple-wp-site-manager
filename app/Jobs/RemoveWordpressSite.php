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
use Throwable;

class RemoveWordpressSite implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $siteId;

    public function __construct(Site $site)
    {
        $this->siteId = $site->id;
    }

    public function handle(ServerManagerService $server): void
    {
        $site = Site::find($this->siteId);

        if (! $site) {
            Log::warning('RemoveWordpressSite: site not found', ['site_id' => $this->siteId]);
            return;
        }

        try {
            $site->update(['status' => 'deploying']);
            $server->remove($site);
        } catch (Throwable $e) {
            $site->update(['status' => 'failed']);
            Log::error('RemoveWordpressSite failed', [
                'site_id' => $site->id,
                'error' => $e->getMessage(),
            ]);
            return;
        }

        $site->delete();
    }
}
