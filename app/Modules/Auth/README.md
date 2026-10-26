# Auth module

Owns sign-in, server-side sessions, token refresh, and sign-out.

- `Controllers/AuthController.php`: sign-in and sign-out request handling.
- `Services/AuthService.php`: Supabase Auth password, refresh, user, and logout APIs.
- `Views/login.php`: accessible sign-in form.
- `Requests/`: future registration and recovery validation.
- `Repositories/`: add user-profile persistence here when needed.

Registration and password recovery screens are not implemented. Create initial
users in Supabase Authentication. Session identity is verified through `/auth/v1/user`.
Vercel stores session state in encrypted HttpOnly cookies keyed by server-only
`APP_KEY`; local development uses PHP session files. Browser JavaScript cannot
read those cookies. See [deployment setup](../../../docs/DEPLOYMENT.md).
