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
