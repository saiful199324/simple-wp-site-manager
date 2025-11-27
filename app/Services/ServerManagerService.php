<?php

namespace App\Services;

use App\Models\Site;
use Spatie\Ssh\Ssh;

class ServerManagerService
{
    protected function ssh(Site $site): Ssh
    {
        return Ssh::create(
            $site->server_user,
            $site->server_host,
            $site->server_port
        );
    }

    protected function basePath(Site $site): string
    {
        return "/opt/wp-sites/{$site->id}";
    }

    protected function writeCompose(Site $site, string $yaml): void
    {
        $basePath = $this->basePath($site);
        $ssh = $this->ssh($site);

        $ssh->execute("mkdir -p {$basePath}");

        $ssh->execute("cat > {$basePath}/docker-compose.yml << 'EOF'
{$yaml}
EOF");
    }

    public function deploy(Site $site): void
    {
        $yaml = $this->generateDockerCompose($site);

        $this->writeCompose($site, $yaml);
        $this->ssh($site)->execute("cd {$this->basePath($site)} && docker compose up -d");
    }

    public function redeploy(Site $site): void
    {
        $yaml = $this->generateDockerCompose($site);

        $this->writeCompose($site, $yaml);
        $this->ssh($site)->execute("cd {$this->basePath($site)} && docker compose pull && docker compose up -d --force-recreate");
    }

    public function start(Site $site): void
    {
        $this->ssh($site)->execute("cd {$this->basePath($site)} && docker compose up -d");
    }

    public function stop(Site $site): void
    {
        $basePath = $this->basePath($site);
        $this->ssh($site)->execute("cd {$basePath} && docker compose stop");
    }

    public function remove(Site $site): void
    {
        $basePath = $this->basePath($site);
        $this->ssh($site)->execute("cd {$basePath} && docker compose down && rm -rf {$basePath}");
    }

    protected function generateDockerCompose(Site $site): string
    {
        return <<<YAML
version: '3.8'

services:
  db:
    image: mysql:5.7
    container_name: {$site->container_name}_db
    restart: always
    environment:
      MYSQL_DATABASE: {$site->db_name}
      MYSQL_USER: {$site->db_user}
      MYSQL_PASSWORD: {$site->db_password}
      MYSQL_ROOT_PASSWORD: {$site->db_password}_root
    volumes:
      - {$site->container_name}_db_data:/var/lib/mysql

  wordpress:
    image: wordpress:latest
    container_name: {$site->container_name}
    restart: always
    depends_on:
      - db
    ports:
      - "{$site->http_port}:80"
    environment:
      WORDPRESS_DB_HOST: db:3306
      WORDPRESS_DB_USER: {$site->db_user}
      WORDPRESS_DB_PASSWORD: {$site->db_password}
      WORDPRESS_DB_NAME: {$site->db_name}
    volumes:
      - {$site->container_name}_wp_data:/var/www/html
    labels:
      wp_site_id: "{$site->id}"
      wp_monitor_token: "{$site->monitor_token}"

volumes:
  {$site->container_name}_db_data:
  {$site->container_name}_wp_data:
YAML;
    }
}
