# Deploy BuligAI to Vercel

Vercel hosts the PHP application. Supabase provides Auth, PostgreSQL, and private
file storage. PHPMailer connects to your SMTP provider. This deployment does not
use a Dockerfile or an application container.

PHP is a **community runtime** on Vercel. This project pins `vercel-php@0.9.0`,
which supports PHP 8.5, and Node 22 for its wrapper. The runtime installs locked
Composer production dependencies. The Composer `vercel` script checks the real
runtime's platform requirements after installation.

References: [Vercel runtimes](https://vercel.com/docs/functions/runtimes) and
[the community PHP runtime](https://github.com/vercel-community/php).

## Project and environment setup

1. Push this project to your GitHub repository and create a Vercel project.
   Select framework **Other**, repository root, and Node **22.x**. Leave Build
   Command and Install Command at their defaults. `vercel.json` sets static output
   to `public`. Do not set the repository root to `public`; the PHP function also
   needs `app`, `bootstrap`, and `config`.
2. Apply the storage migration to your hosted Supabase project using
   [Supabase setup](SUPABASE.md). CI does not modify a live database.
3. Generate a random encryption key locally:

   ```powershell
   php -r 'echo base64_encode(random_bytes(32)), PHP_EOL;'
   ```

4. Add the following values to Vercel's **Production environment variables**:

   | Variable | Value |
   | --- | --- |
   | `APP_ENV` | `production` |
   | `APP_URL` | Your public HTTPS app URL |
   | `APP_KEY` | The generated base64-encoded 32-byte secret |
   | `SESSION_DRIVER` | `cookie` |
   | `SUPABASE_URL` | Your HTTPS Supabase project URL |
   | `SUPABASE_PUBLISHABLE_KEY` | Your `sb_publishable_...` key |
   | `SUPABASE_STORAGE_BUCKET` | `community-files` |

   Add `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`,
   `MAIL_ENCRYPTION`, `MAIL_FROM_ADDRESS`, and `MAIL_FROM_NAME` for application
   email. Keep the session key and SMTP password server-only. Use the same
   `APP_KEY` across deployments; changing it signs everyone out. Set equivalent
   values in Preview only if you deliberately enable preview deployments.
5. Create an initial email/password user in Supabase Authentication.

Missing or invalid `APP_KEY` causes a configuration error on Vercel rather than
falling back to unreliable filesystem sessions. The local `.env` file and session
directory are excluded from uploads.

## GitHub Actions production deployment

The workflow has these jobs:

| Job | What it checks |
| --- | --- |
| PHP quality | Composer configuration, syntax, PSR-12, PHPUnit, dependency audit |
| Supabase policy tests | RLS tests against a disposable local Supabase stack |
| HTTP smoke (file/cookie) | Entry point, sign-in, private routes, source/config isolation, cross-request CSRF state |
| Deploy to Vercel | Pulls settings, builds production output, deploys the prebuilt artifact |

The application does not use Docker. The policy-test job uses Docker solely to
run its disposable Supabase stack. That stack has no connection to your hosted project.

Link the Vercel project locally to obtain its identifiers:

```powershell
npx.cmd --yes vercel@65.2.0 login
npx.cmd --yes vercel@65.2.0 link
```

On macOS/Linux, use `npx` instead of `npx.cmd`. Add these GitHub secrets, either
at repository level or in its `production` environment:

| Secret | Source |
| --- | --- |
| `VERCEL_TOKEN` | A Vercel account token with access to the intended project |
| `VERCEL_ORG_ID` | `orgId` in the generated `.vercel/project.json` |
| `VERCEL_PROJECT_ID` | `projectId` in the generated `.vercel/project.json` |

Set the **repository variable** `DEPLOY_ENABLED=true`. Push to `main`, or run the
workflow manually against `main`. Deployment waits for all check jobs. Protect
`main` with those jobs as required checks if your team uses branch protection.

`vercel.json` disables automatic Git deployments so they do not bypass the
Actions checks. The production job uses the official
[pull → build → deploy --prebuilt workflow](https://vercel.com/kb/guide/how-can-i-use-github-actions-with-vercel).
No build artifacts or pulled production environment files are published as CI artifacts.

## Deploy directly from your computer

After project/environment setup and login, a manual deployment can use:

```powershell
npx.cmd --yes vercel@65.2.0 --prod
```

Vercel builds the PHP function remotely. The community runtime's native Linux
build is not a supported local Windows build target; use the remote command or
the supplied Ubuntu GitHub Actions job. For local app development use
`composer serve`; the PHP runtime does not support `vercel dev`.

## Serverless sessions and file limits

Vercel automatically selects encrypted cookie sessions. AES-256-GCM protects
the session's confidentiality and integrity. Cookies are HttpOnly, Secure,
SameSite=Lax, and have a seven-day sliding expiry. Vercel uses a host-only cookie
name. Tokens are decrypted only on the server and Supabase verifies the user on
every private-file action. No Redis or separate session database is required.

Cookie values are capped at 3,800 characters. Avoid oversized custom JWT claims;
state that cannot fit is rejected. Sign-out clears the cookie and asks Supabase
to revoke the current refresh session. As with Supabase Auth generally, an
already-issued access token can remain usable until its expiry.

Vercel's [function payload limit](https://vercel.com/docs/functions/limitations)
is 4.5 MB. PHP-proxied uploads are capped at **3 MiB**, including MIME validation,
with room for multipart overhead. Local uploads and the private bucket retain a
10 MiB cap. A larger browser upload would need a separate direct-Storage flow.

Downloads validate the signed-in owner and create a Supabase attachment URL
valid for **60 seconds**. Binary data goes directly from Supabase to the browser.
The URL acts as a temporary bearer link; its holder can use it until expiry.

Static routes allow only approved filename patterns under `public/assets`.
Other URLs go through the PHP application; `.env`, Composer files, application
source, and session directories are not static routes. PHP temporary uploads
use `/tmp`; persistent files stay in Supabase Storage.

## Verify and roll back

After deployment, check `/health`, `/login`, and `/.env` (404), then perform the
two-user upload/download/delete procedure in [SUPABASE.md](SUPABASE.md). Check a
file larger than 3 MiB is rejected by the app. Send a deliberate test email only
after configuring your SMTP provider; local `composer mail:test` can verify it.

Promote a known-good Vercel deployment to roll back application code. Keep its
`APP_KEY` compatible if sessions should survive. Database migrations are separate
and need their own corrective migration or backup/restore procedure.

Local checks do not prove a Vercel deployment or hosted Supabase policies work.
A real build/deploy requires an authenticated linked Vercel project, runtime
environment variables, and SMTP/Supabase credentials for their live checks.
