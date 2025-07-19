# Dashboard View

The dashboard shows a quick overview of your budget status.

Visit the home page `/` after logging in to see:

- **Income, Expenses, Net** cards for the current month
- A bar chart comparing spending vs. each budget limit
- Progress rings for all savings goals

The data comes from `/api/transactions`, `/api/budget-check` and `/api/savings-goals` endpoints. Ensure you have a valid JWT token stored via the Auth store.

