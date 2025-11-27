<?php

namespace App\Console\Commands;

use App\Jobs\RedeployWordpressSite;
use App\Models\Site;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class BackfillMonitor extends Command
{
    protected $signature = 'sites:backfill-monitor {--redeploy : Queue a redeploy for updated sites}';

    protected $description = 'Set missing monitor tokens and optionally redeploy to refresh labels.';

    public function handle(): int
    {
        $redeploy = $this->option('redeploy');
        $updated = 0;

        Site::whereNull('monitor_token')->chunkById(100, function ($sites) use (&$updated, $redeploy) {
            foreach ($sites as $site) {
                $site->monitor_token = Str::random(40);
                $site->save();
                $updated++;

                if ($redeploy) {
                    dispatch(new RedeployWordpressSite($site));
                }
            }
        });

        $this->info("Updated {$updated} site(s).");

        if ($redeploy) {
            $this->info('Redeploy jobs have been queued for the updated sites.');
        } else {
            $this->info('Run with --redeploy to refresh docker-compose labels on the VPS.');
        }

        return Command::SUCCESS;
    }
}
