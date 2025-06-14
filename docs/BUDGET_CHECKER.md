# Budget Checker

Use `/api/budget-check` to compare your spending with `BudgetLimit` settings. The endpoint returns totals for the month and creates notifications when limits are exceeded.

## Request

`GET /api/budget-check?month=YYYY-MM`

- `month` is optional. Defaults to the current month.

```bash
curl -H "Authorization: Bearer <token>" \
  http://localhost:8000/api/budget-check?month=2025-01
```

## Response

```json
{
  "data": [
    { "category": 1, "spent": 110, "limit": 100, "over": true },
    { "category": 2, "spent": 150, "limit": 200, "over": false }
  ]
}
```
