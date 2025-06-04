# Agent Guidance

## Project Overview

This repository is a Symfony 7.3 project using PHP (8.2+) and Vue 3 via Webpack Encore. JavaScript, CSS and Vue components live in the `assets/` directory and are compiled to `public/build` using `npm run build`. The PHP backend currently contains a single controller (`HomeController`) and Twig base template.

## Code Style

- Follow `.editorconfig` for spacing:
  - 4 spaces per indent for most files
  - 2 spaces for `compose.yaml`/`compose.*.yaml`
  - Markdown files should not trim trailing whitespace.
- Use Unix newlines and include a trailing newline at EOF.

## Development Notes

- Node and npm are available. After checking out the project, run `npm install` once to install dependencies if `node_modules` is missing.
- To verify the front-end build, run `npm run build`. This compiles assets to `public/build`.
- PHP and Composer are **not** installed in the environment, so you cannot run PHP unit tests or composer scripts. Focus on front-end checks only.
- There are currently no automated tests.

## Commit Guidance

- Keep commit messages concise, prefixed with a short summary followed by a blank line and additional details if necessary.
- Ensure `npm run build` completes successfully before committing changes to JavaScript, CSS, or Vue files.

