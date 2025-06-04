# Budgertia

This project is a small Symfony 7 application with a Vue 3 frontend built
using Webpack Encore. Docker images are provided for local development.

## Prerequisites

- [Docker](https://docs.docker.com/get-docker/) with Compose support
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

## Testing and Code Quality

Run the following commands inside the container to execute the test suite and quality tools:

```bash
docker compose run --rm app composer test
docker compose run --rm app composer phpstan
docker compose run --rm app composer phpcs
```
