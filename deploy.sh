#!/bin/bash
# Pulls the latest code from GitHub and redeploys, but only if there's
# actually a new commit. Safe to run from a webhook, a cron job, or by hand
# — concurrent/duplicate runs are skipped via the lock file below.
set -e
cd "$(dirname "$0")"

LOCK_FILE="storage/deploy.lock"
LOG_FILE="storage/logs/deploy.log"
mkdir -p storage/logs

if [ -f "$LOCK_FILE" ]; then
    if [ "$(find "$LOCK_FILE" -mmin +10)" ]; then
        echo "$(date) :: stale lock file found, removing it" >> "$LOG_FILE"
        rm -f "$LOCK_FILE"
    else
        echo "$(date) :: another deploy is already running, skipping" >> "$LOG_FILE"
        exit 0
    fi
fi
touch "$LOCK_FILE"
trap 'rm -f "$LOCK_FILE"' EXIT

git fetch origin main >> "$LOG_FILE" 2>&1

LOCAL=$(git rev-parse HEAD)
REMOTE=$(git rev-parse origin/main)

if [ "$LOCAL" = "$REMOTE" ]; then
    echo "$(date) :: no new commits, nothing to deploy" >> "$LOG_FILE"
    exit 0
fi

{
    echo "$(date) :: deploying $LOCAL -> $REMOTE"
    git merge --ff-only origin/main
    composer install --no-dev --optimize-autoloader --no-interaction
    php artisan migrate --force
    php artisan storage:link || true
    php artisan config:clear
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    chmod -R 775 storage bootstrap/cache
    echo "$(date) :: deploy finished successfully"
} >> "$LOG_FILE" 2>&1
