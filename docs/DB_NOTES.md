# Database Notes

The `Transaction` entity now maps to the `transactions` table instead of the reserved keyword `transaction`. Run migrations to create or update the schema:

```bash
docker compose run --rm app php bin/console doctrine:migrations:migrate
```

User roles are stored as a JSON array in the `roles` column added by `Version202501010002`. Run the migration after pulling updates.
