# Vercel deployment implementation plan

**Goal:** Run the existing PHP/Supabase application on Vercel without app Docker hosting.

**Architecture:** Community PHP 8.5 runtime, encrypted cookie state for serverless
requests, Supabase Auth/Storage, signed downloads, and guarded GitHub deployment.

- [x] Add failing tests for encrypted cookie confidentiality/integrity/expiry/key
  validation and Vercel upload limit configuration.
- [x] Implement `CookieSession` and `SessionManager`; connect entry point and auth
  controller session rotation without writing serverless session files.
- [x] Test and implement signed Storage downloads; apply 3 MiB Vercel upload limits
  to validation and UI while retaining the existing local 10 MiB limit.
- [x] Add `api/index.php`, `api/php.ini`, `vercel.json`, `.vercelignore`, Node runtime
  metadata, and HTTP smoke command. Remove Railway/application Docker files.
- [x] Replace container/Railway workflow jobs with HTTP checks and Vercel deployment;
  retain Supabase policy tests and their isolated Docker requirement.
- [x] Update README, concept, module notes, environment example, and deployment
  guide with exact setup steps, session key generation, and platform limits.
- [x] Run Composer validation/check/audit, HTTP smoke checks for both session
  drivers, configuration parsing, and review. Record external checks not run.

## Verification result

- Composer validation, syntax, PSR-12 and platform requirements pass.
- PHPUnit passes: 37 tests, 75 assertions, including idle-session sign-out revocation.
- Both local file-session and serverless cookie-session HTTP smoke checks pass.
- Vercel configuration validates against the downloaded official schema; static
  routing, workflow dependencies, Supabase TOML and Markdown links were checked.
- Composer audit reports no known vulnerabilities.
- Review fixes include explicit `public` static output paths, consistent cache
  headers, and refreshing expired access tokens before revoking a logout session.
- A real Vercel build/deploy still requires project credentials and environment
  variables. Live Supabase/SMTP integration and Docker-backed pgTAP were not run
  locally because their services/credentials or Docker are unavailable.

The user requested publication to `https://github.com/regalakhing-tech/BuligAI.git`.
Its remote had no branches when inspected. Publish the verified initial project
on `main`; credentials, dependencies, caches and local session files stay ignored.
