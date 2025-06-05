# Database Notes

The `Transaction` entity now maps to the `transactions` table instead of the
reserved keyword `transaction`. Run migrations to create or update the schema:

```bash
docker compose run --rm app php bin/console doctrine:migrations:migrate
```

If you previously attempted migrations and have a table named `transaction`, the new migration `Version20250605090259` will automatically rename it to `transactions`.

