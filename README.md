# Simple WP Site Manager

Laravel 12 + Inertia.js + React dashboard to manage Docker-based WordPress sites on remote VPS hosts. Each site lives in its own container stack; you can create, edit, start/stop, redeploy, and delete sites. A lightweight monitor script reports container status back to the app every 5 minutes.

## Features
- Site CRUD with validation (domain, SSH host/user/port, DB creds, HTTP port).
- Remote orchestration over SSH (Spatie SSH) to write a per-site `docker-compose.yml`, start/stop, redeploy, and remove stacks.
- Status tracking: Running, Stopped, Deploying, Failed; includes monitor token per site.
- Monitoring script (`scripts/docker-monitor.sh`) that logs to `/var/log/docker-monitor.log` and posts status to the app API.
- Encrypted storage for sensitive fields (server/db data) at rest.

## Quick start (local)
1. Requirements: PHP 8.2+, Composer, Node 20+, npm, a local database.
2. Install deps and build assets:
   ```bash
   composer install
   npm install
   npm run build
   cp .env.example .env
   php artisan key:generate
   php artisan migrate --seed
   ```
3. Run dev stack (Laravel serve, queue listener, Vite):
   ```bash
   npm run dev
   ```
   Make sure `QUEUE_CONNECTION=database` (or another queue driver) is set so jobs run.

## Remote server requirements
- Docker + docker compose plugin installed.
- SSH access for the configured user; key-based auth recommended.
- The app writes site files under `/opt/wp-sites/{site_id}` on the VPS.

## Deploy/start/stop flows
- Creating a site triggers `DeployWordpressSite` (writes compose, `docker compose up -d`).
- Updating a site triggers `RedeployWordpressSite` (rewrite compose, pull, recreate).
- Start/Stop buttons dispatch jobs to `docker compose up -d` or `docker compose stop`.
- Delete triggers `RemoveWordpressSite` (compose down, remove path, then deletes the record).

## Monitoring
1. Copy the script to the VPS:
   ```bash
   scp scripts/docker-monitor.sh user@your-vps:/usr/local/bin/docker-monitor.sh
   ssh user@your-vps "chmod +x /usr/local/bin/docker-monitor.sh"
   ```
2. Set environment for the script (app URL):
   ```bash
   echo 'API_URL=https://your-app-domain/api/site-status' | sudo tee /etc/default/wp-monitor
   ```
   Then source it in cron (next step).
3. Add a cron job (every 5 minutes):
   ```
   */5 * * * * . /etc/default/wp-monitor; /usr/local/bin/docker-monitor.sh
   ```
4. The script appends to `/var/log/docker-monitor.log` and posts status with the per-site monitor token baked into container labels.

## API endpoint for monitor
- `POST /api/site-status`
- Headers: `X-Monitor-Token: {monitor_token}`
- Body: `{"container_name": "wp_example_com", "status": "running|exited|restarting|..." }`

## Notes & defaults
- Each site maps its container port 80 to a host `http_port` you provide (e.g. 8080, 8081). Ensure uniqueness per host or front a reverse proxy.
- Sensitive fields (`server_host`, `server_user`, `db_name`, `db_user`, `db_password`) are encrypted at rest via Laravel casts.
- Container labels include `wp_site_id` and `wp_monitor_token` so the monitor can discover them.

## Useful scripts
- Run tests: `composer test`
- Seed sample sites: `php artisan db:seed --class=SiteSeeder`

## Repository
Push to a public repo named `simple-wp-site-manager` when ready. No vendor-specific naming is used.
