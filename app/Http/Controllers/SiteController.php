<?php

namespace App\Http\Controllers;
use App\Models\Site;
use App\Jobs\DeployWordpressSite;
use App\Jobs\RemoveWordpressSite;
use App\Jobs\StartWordpressSite;
use App\Jobs\StopWordpressSite;
use App\Jobs\RedeployWordpressSite;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class SiteController extends Controller
{
    public function index()
    {
        $sites = Site::latest()->get()->map(function (Site $site) {
            // ensure we never expose monitor token to the frontend
            unset($site->monitor_token);
            return $site;
        });

        return Inertia::render('Sites/Index', [
            'sites' => $sites,
        ]);
    }

    public function create()
    {
        return Inertia::render('Sites/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'domain'      => 'required|string|max:255|unique:sites,domain',
            'server_host' => 'required|string',
            'server_port' => 'required|integer',
            'server_user' => 'required|string',
            'db_name'     => 'required|string',
            'db_user'     => 'required|string',
            'db_password' => 'required|string',
            'http_port'   => 'required|integer|min:1|max:65535|unique:sites,http_port',
        ]);

        $data['container_name'] = 'wp_' . str_replace('.', '_', $data['domain']);
        $data['status'] = 'deploying';
        $data['monitor_token'] = Str::random(40);

         // 1) Save in DB
    $site = Site::create($data);

    // 2) Trigger deploy job (this will SSH into VPS, create /opt/wp-sites/{id}, run docker compose)
    dispatch(new DeployWordpressSite($site));

    return redirect()->route('sites.index')
        ->with('success', 'Site created and deployment started.');
    }

    public function edit(Site $site)
    {
        return Inertia::render('Sites/Edit', ['site' => $site]);
    }

    public function update(Request $request, Site $site)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'server_host' => 'required|string',
            'server_port' => 'required|integer',
            'server_user' => 'required|string',
            'db_name'     => 'required|string',
            'db_user'     => 'required|string',
            'db_password' => 'required|string',
            'http_port'   => 'required|integer|min:1|max:65535|unique:sites,http_port,' . $site->id,
        ]);

        $site->update($data);

        // re-deploy with the new settings
        dispatch(new RedeployWordpressSite($site));

        return redirect()->route('sites.index');
    }

    public function destroy(Site $site)
    {
         dispatch(new RemoveWordpressSite($site));
        return redirect()->route('sites.index')
        ->with('success', 'Delete requested. Remote stack will be cleaned up.');
    }

    public function start(Site $site)
    {
        dispatch(new StartWordpressSite($site));

        return redirect()->route('sites.index')
            ->with('success', 'Start requested. This may take a minute.');
    }

    public function stop(Site $site)
    {
        dispatch(new StopWordpressSite($site));

        return redirect()->route('sites.index')
            ->with('success', 'Stop requested.');
    }

    public function redeploy(Site $site)
    {
        dispatch(new RedeployWordpressSite($site));

        return redirect()->route('sites.index')
            ->with('success', 'Redeploy requested.');
    }
}
