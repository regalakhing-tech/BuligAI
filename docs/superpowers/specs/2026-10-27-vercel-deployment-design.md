# Vercel deployment adaptation

The user selected Vercel and Supabase. Replace the Railway and application Docker
deployment configuration with a Vercel PHP 8.5 function (`vercel-php@0.9.0`).
Route approved public assets to their static files and all application requests
to `api/index.php`. Bundle Composer production dependencies through the runtime.

Vercel function filesystems do not provide durable shared PHP session storage.
Add authenticated encryption for HttpOnly, Secure, SameSite cookies, keyed by a
server-only, base64-encoded 32-byte `APP_KEY`. Use this driver automatically on
Vercel; retain file sessions for local development. Store only existing auth,
CSRF, and flash state, validate cookie age and size, rotate encryption nonces on
every write, and verify Supabase identity before accessing private files.

Vercel limits request and response payloads to 4.5 MB. Limit PHP-proxied uploads
to 3 MiB on Vercel; local uploads remain capped at 10 MiB. Keep the bucket's
existing 10 MiB limit. Return short-lived signed Supabase download links rather
than proxy file bytes through the function. MIME and ownership checks remain.

GitHub Actions runs PHP checks, HTTP smoke checks, and disposable Supabase policy
tests. Production deployment requires those jobs, `DEPLOY_ENABLED=true`, a main
branch trigger, and Vercel credentials. Use Vercel's pull/build/deploy-prebuilt
sequence. Update current README, concept, and setup docs for this final design.
Docker is used only by optional local Supabase tests and that CI policy job;
the application has no Dockerfile, container build, or Railway configuration.

Verify encryption round trips, wrong-key/tampered/expired cookie rejection,
per-environment upload limits, safe signed links, public routes, and unchanged
authorization tests. A real Vercel build/deployment requires an authenticated,
linked project; no token or project is currently configured.
