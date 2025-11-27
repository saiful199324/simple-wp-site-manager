<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Site;

class SiteStatusController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'container_name' => 'required|string',
            'status'         => 'required|string',
            'site_id'        => 'nullable|integer',
        ]);

        $token = $request->header('X-Monitor-Token');

        if (! $token) {
            return response()->json(['error' => 'token missing'], 401);
        }

        // Prefer token match; fall back to site_id or container name
        $site = Site::where('monitor_token', $token)->first();

        if (! $site && ! empty($data['site_id'])) {
            $site = Site::find($data['site_id']);
        }

        if (! $site) {
            $site = Site::where('container_name', $data['container_name'])->first();
        }

        if (! $site) {
            return response()->json(['error' => 'site not found'], 404);
        }

        if ($site->monitor_token !== $token) {
            return response()->json(['error' => 'unauthorized'], 401);
        }

        $normalizedStatus = match (strtolower($data['status'])) {
            'running' => 'running',
            'up', 'healthy' => 'running',
            'stopped', 'exited' => 'stopped',
            'restarting', 'starting', 'created' => 'deploying',
            default => 'failed',
        };

        $site->update([
            'status' => $normalizedStatus,
            'last_reported_at' => now(),
        ]);

        return response()->json(['message' => 'status updated']);
    }
}
