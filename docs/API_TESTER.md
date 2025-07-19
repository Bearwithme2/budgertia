# API Tester

Visit `/tester` in your browser to interact with Budgertia's REST API.  
Ensure frontend assets are compiled with `npm run build` before visiting.

1. Enter a JWT token from `/api/login` or `/api/register`.  
2. Choose the HTTP method.  
3. Provide the endpoint path, e.g. `/api/categories`.  
4. Enter a JSON body for POST or PUT requests.  
5. Click **Send** to view the status and response body.  
6. Missing or invalid fields will show a red error message above the response.  
7. JSON responses are prettified for easier reading.

Responses now include a `meta.requestId` so you can trace individual calls.

8. Use `/api/budget-check` to view monthly totals.
9. Manage alerts via `/api/notifications` and stream updates from `/api/notifications/stream?token=<JWT>`.

## Preset Examples

Use these requests as a starting point in the tester:

- List categories
  - Method: `GET`
  - Endpoint: `/api/categories`
- Create category
  - Method: `POST`
  - Endpoint: `/api/categories`
  - Body:
    ```json
    { "name": "Groceries" }
    ```
- Budget check
  - Method: `GET`
  - Endpoint: `/api/budget-check?month=2025-06`
- Notifications stream
  - Method: `GET`
  - Endpoint: `/api/notifications/stream?token=<JWT>`
  - See `docs/examples/notifications.json` for a sample payload.
