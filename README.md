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

3. Generate JWT keys:

    ```bash
docker compose run --rm app php bin/console lexik:jwt:generate-keypair --no-interaction --skip-if-exists
    ```

4. Start the stack:

    ```bash
    docker compose up --build
    ```

## Architecture

Controllers delegate logic to services. Request bodies are mapped to DTOs via a
custom argument resolver. Responses are wrapped using `ApiResponseFactory`
providing a `meta.requestId` for tracing.

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
npm run lint
npm run test
```

See `docs/API_USAGE.md` for REST API usage.
See `docs/JWT_KEYS.md` for generating JWT keys.
See `docs/NOTIFICATIONS.md` for how notifications work.
See `docs/BUDGET_CHECKER.md` for the budget checker endpoint.
See `docs/PRODUCTION.md` for deploying with Docker Compose.
