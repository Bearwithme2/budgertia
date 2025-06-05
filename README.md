# Budgertia

This project is a small Symfony 7 application with a Vue 3 frontend built
using Webpack Encore. Docker images are provided for local development. The
backend stores data in a SQLite database and Doctrine migrations are included
to manage the schema.

## Prerequisites

- [Docker](https://docs.docker.com/get-docker/) with Compose support
- PHP 8.2 and Composer (provided by `.setup/setup.sh`)
- Node.js 18+ and npm

## Installation

1. Install JavaScript packages and build the frontend assets:

   ```bash
   npm install
   npm run build
   ```

2. Install PHP dependencies inside the container:

   ```bash
   docker compose run --rm app composer install
   ```

3. Start the stack:

   ```bash
   docker compose up --build
   ```

The application will be available at [http://localhost:8000](http://localhost:8000).

## Database & Migrations

Run Doctrine migrations after installing the PHP dependencies:

```bash
docker compose run --rm app php bin/console doctrine:migrations:migrate
```

## Testing and Code Quality

Run the following commands inside the container to execute the test suite and quality tools. Codex uses `codex.custom.yml` to run these commands only when code changes:

```bash
docker compose run --rm app composer test
docker compose run --rm app composer phpstan
docker compose run --rm app composer phpcs
```

See `docs/API_USAGE.md` for REST API usage.
