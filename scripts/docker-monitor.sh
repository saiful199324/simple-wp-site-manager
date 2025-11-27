#!/usr/bin/env bash

# Poll Docker containers labeled with wp_site_id and send their status back
# to the Laravel app. Writes a log line for each check.

API_URL="${API_URL:-https://your-app-host.test/api/site-status}"
LOG_FILE="${LOG_FILE:-/var/log/docker-monitor.log}"
DATE_CMD="${DATE_CMD:-date -Is}"

if ! command -v docker >/dev/null 2>&1; then
  echo "docker not installed; exiting" >&2
  exit 1
fi

containers=$(docker ps -a --filter "label=wp_site_id" --format "{{.Names}}")

if [ -z "$containers" ]; then
  exit 0
fi

for name in $containers; do
  status=$(docker inspect -f '{{.State.Status}}' "$name")
  token=$(docker inspect -f '{{ index .Config.Labels \"wp_monitor_token\" }}' "$name")
  site_id=$(docker inspect -f '{{ index .Config.Labels \"wp_site_id\" }}' "$name")
  compose_name=$(docker inspect -f '{{.Name}}' "$name" | sed 's#^/##')
  ts=$($DATE_CMD)

  if [ -n "$API_URL" ] && [ -n "$token" ]; then
    curl -s -X POST "$API_URL" \
      -H "Content-Type: application/json" \
      -H "X-Monitor-Token: $token" \
      -d "{\"container_name\":\"$compose_name\",\"status\":\"$status\",\"site_id\":\"$site_id\"}" >/dev/null 2>&1
  fi

  echo "$ts $compose_name $status" >> "$LOG_FILE"
done
