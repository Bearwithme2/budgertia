# Production Setup

Use `docker-compose.prod.yml` to build and run Budgertia in production.
It mounts the SQLite database to the `sqlite-data` volume and runs a cron
container that triggers `app:generate-notifications` nightly.

## Build and Start

```bash
docker compose -f docker-compose.prod.yml up --build -d
```

The web server listens on port `8000`.
