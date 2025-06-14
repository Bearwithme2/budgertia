# Notifications

Budgertia stores alerts for each user in the `notification` table.
Use them to surface events like budget overruns.

## Fields

- `message` – text to display
- `level` – info, warning or danger
- `createdAt` – timestamp
- `isRead` – whether the user has seen the alert

## Example

```bash
# list notifications
curl -H "Authorization: Bearer <token>" http://localhost:8000/api/notifications
```

Notifications are generated nightly by the `app:generate-notifications` command.
Run it manually with `php bin/console app:generate-notifications`.

## API

### List unread notifications

`GET /api/notifications?page=1&limit=10`

Returns unread items by default. Pass `isRead=true` to view archived ones.

### Mark as read

`PATCH /api/notifications/{id}/read`

### Mark all as read

`PATCH /api/notifications/read-all`

### Live updates

`GET /api/notifications/stream`

Streams unread notifications as Server-Sent Events.


