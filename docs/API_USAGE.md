# API Usage

All endpoints are prefixed with `/api` and require JWT authentication unless stated otherwise.

## Auth

- `POST /api/register` – create a user
- `POST /api/login` – obtain a token
- `POST /api/token/refresh` – refresh a token

```bash
curl -X POST -H "Content-Type: application/json" \
     -d '{"email":"john@example.com","password":"secret"}' \
     http://localhost:8000/api/register
```

## Category

- `GET /api/categories` – list categories
- `POST /api/categories` – create a category
- `GET /api/categories/{id}` – get a category
- `PUT /api/categories/{id}` – update a category
- `DELETE /api/categories/{id}` – remove a category

## Transaction

- `GET /api/transactions` – list your transactions
- `POST /api/transactions` – create a transaction
- `GET /api/transactions/{id}` – get a transaction
- `PUT /api/transactions/{id}` – update a transaction
- `DELETE /api/transactions/{id}` – delete a transaction

## Budget Limit

- `GET /api/budget-limits` – list your limits
- `POST /api/budget-limits` – create a limit
- `GET /api/budget-limits/{id}` – get a limit
- `PUT /api/budget-limits/{id}` – update a limit
- `DELETE /api/budget-limits/{id}` – remove a limit

## Savings Goal

- `GET /api/savings-goals` – list your goals
- `POST /api/savings-goals` – create a goal
- `GET /api/savings-goals/{id}` – get a goal
- `PUT /api/savings-goals/{id}` – update a goal
- `DELETE /api/savings-goals/{id}` – delete a goal

```bash
curl -X POST -H "Content-Type: application/json" \
     -H "Authorization: Bearer <token>" \
     -d '{"name":"Groceries"}' \
     http://localhost:8000/api/categories
```
