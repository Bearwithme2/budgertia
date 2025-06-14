# Notification Generator

The `app:generate-notifications` command checks each user for budget limit overruns and completed savings goals. It creates notification records automatically.

Run it manually with:

```bash
php bin/console app:generate-notifications
```

The command is scheduled via `cron.schedule` to run nightly at midnight.
