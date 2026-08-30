#!/bin/sh
# Production container entrypoint: wait for the database, make sure the app
# has a key, run pending migrations, then hand off to the given CMD (by
# default `php artisan serve`).
set -eu

DB_HOST="${DB_HOST:-mysql}"
DB_PORT="${DB_PORT:-3306}"

if [ "${DB_CONNECTION:-mysql}" = "mysql" ]; then
    echo "Waiting for MySQL at ${DB_HOST}:${DB_PORT}..."
    tries=0
    until nc -z "$DB_HOST" "$DB_PORT" 2>/dev/null; do
        tries=$((tries + 1))
        if [ "$tries" -ge 60 ]; then
            echo "MySQL did not become reachable in time, giving up." >&2
            exit 1
        fi
        sleep 1
    done
    echo "MySQL is reachable."
fi

# APP_KEY is a real production secret and must be supplied by the
# environment (see README "Known gaps") — this image does not generate or
# persist one for you, since there is nowhere durable to write it to inside
# an immutable container.
if [ -z "${APP_KEY:-}" ]; then
    echo "APP_KEY is not set. Generate one with 'php artisan key:generate --show'" >&2
    echo "and pass it in as the APP_KEY environment variable." >&2
    exit 1
fi

php artisan migrate --force

exec "$@"
