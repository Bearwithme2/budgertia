# Agent Guidance

## Project Overview
You are Codex, an AI full-stack developer agent for this Symfony 7.3 project using PHP 8.2+ and Vue 3 with Webpack Encore. The backend uses Symfony controllers and Twig templates. Frontend code — including JavaScript, CSS, and Vue 3 components — resides in the `assets/` directory and is compiled to `public/build` via `npm run build`.

## Code Style
- Respect `.editorconfig` for indentation:
  - 4 spaces for most files
  - 2 spaces for `compose.yaml` / `compose.*.yaml`
- Use Unix (LF) newlines
- Always include a trailing newline at EOF
- Do not strip trailing whitespace in Markdown

## Development Notes
- PHP 8.2, Composer and Docker are available via `.setup/setup.sh`.
- Use Docker Compose to run the application. Execute quality tools through Composer:
  - `composer test`
  - `composer phpstan`
- `composer phpcs`
- Run Php Inspections (EA Extended) using [Qodana](https://github.com/JetBrains/qodana-cli) or the PhpStorm plugin. A basic command is:
  `docker run --rm -v $(pwd):/data/project jetbrains/qodana-php --fail-threshold 0`
- Node.js and npm are available. Run `npm install` if `node_modules` is missing.
- Validate all front-end changes using `npm run build`.
- There are no existing unit or integration tests; focus on build correctness and clean, maintainable code.
- Ensure the `/api/register` endpoint remains publicly accessible in `security.yaml` so new users can sign up without a token.

## Best Practices
### Symfony & PHP (8.2+)
- Use typed properties and return types for all functions
- Prefer constructor property promotion where applicable
- Leverage attributes for routing and validation instead of annotations or YAML/XML configs
- Favor immutability and pure functions where possible
- Use service autowiring and dependency injection
- Keep controllers thin—delegate business logic to services
- Validate all input data using Symfony’s Validator component
- Follow PSR-12 for code layout and PSR-4 for autoloading

### Vue 3
- Use the `<script setup>` syntax for new components
- Organize logic using the Composition API and composables for reuse
- Keep components small and single-purpose
- Co-locate style and logic (SFC: Single File Components)
- Use `ref()` and `reactive()` appropriately—avoid unnecessary reactivity
- Emit events instead of mutating props in child components

### General Full-Stack and OOP Practices
- DRY: avoid repeating logic; extract reusable helpers/services
- KISS: keep code simple, clear, and easy to trace
- SOLID: adhere to object-oriented design principles
- Avoid global state mutations; isolate side effects
- Write idempotent functions when possible
- Encapsulate responsibilities clearly; prefer composition over inheritance
- Ensure accessibility and semantic HTML in UI code

## Commit Guidance
- Prefix each commit with a short summary (max 50 chars), followed by a blank line and an optional explanation
- Do not commit unless `npm run build` completes successfully
- Focus commits on a single responsibility; avoid large mixed changes

## Codex Automation
Codex reads the `codex.custom.yml` file in the project root to know when to run automated tasks. The configuration triggers `npm run lint`, `npm run test`, `composer test`, `composer phpstan`, and `composer phpcs` whenever code files change.

