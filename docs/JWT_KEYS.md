# JWT Key Setup

Budgertia uses LexikJWTAuthenticationBundle for stateless API authentication. Generate a key pair before running the app.

```bash
# inside the project root
mkdir -p config/jwt
php bin/console lexik:jwt:generate-keypair --skip-if-exists --no-interaction
```

The command reads `JWT_PASSPHRASE` from your environment. Keys are stored in `config/jwt/private.pem` and `config/jwt/public.pem`. Regenerate them anytime by rerunning the command with `--overwrite`.

