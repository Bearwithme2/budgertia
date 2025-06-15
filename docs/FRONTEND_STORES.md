# Frontend Stores

Budgertia uses Pinia to manage client-side state. The stores live in `assets/stores` and wrap the REST API.

## Auth Store

- **File**: `assets/stores/auth.js`
- `login(email, password)` – POST `/api/login` and save the token.
- `register(email, password)` – POST `/api/register` and save the token.
- `refresh()` – POST `/api/token/refresh` to update the token.
- `logout()` – remove the token from storage.

## Budget Store

- **File**: `assets/stores/budget.js`
- `loadLimits()` – GET `/api/budget-limits`.
- `addLimit(amount, category)` – POST `/api/budget-limits`.
- `updateLimit(id, data)` – PUT `/api/budget-limits/{id}`.
- `deleteLimit(id)` – DELETE `/api/budget-limits/{id}`.

## Notifications Store

- **File**: `assets/stores/notifications.js`
- `fetchList(params)` – GET `/api/notifications`.
- `markRead(id)` – PATCH `/api/notifications/{id}/read`.
- `markAllRead()` – PATCH `/api/notifications/read-all`.

Add Pinia to your Vue app:

```javascript
import { createPinia } from 'pinia';
import { createApp } from 'vue';
import App from './vue/App.vue';

createApp(App).use(createPinia()).mount('#app');
```

