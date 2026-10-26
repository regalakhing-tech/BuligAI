# BuligAI foundation implementation plan

Historical implementation record. Deployment now follows
[the Vercel plan](2026-10-27-vercel-deployment.md).

**Goal:** Create a runnable, documented modular PHP project with private files.

**Architecture:** PSR-4 modular monolith; Guzzle calls Supabase REST endpoints
with each user's token; PHPMailer handles application SMTP.

**Tech stack:** PHP 8.5, Composer 2, Supabase Auth and Storage, PHPMailer 7,
PHPUnit 12, Apache, Docker, GitHub Actions, and Railway.

## Tasks

- [x] Foundation: create Composer configuration, environment template, module
  ownership documentation, public entry point, configuration, and shared helpers.
- [x] Tests first: write behavioral tests in `tests/Unit` for MIME and size
  validation, path ownership, authenticated Storage requests, auth token refresh,
  CSRF rejection, and SMTP settings. Run `composer test` and observe failures.
- [x] Implement the tested boundaries in `app/Shared` and
  `app/Modules/{Auth,Files,Notifications}`. Run `composer test` until green.
- [x] Build `/login`, `/files`, upload, download, delete, and logout routes with
  server-side sessions, CSRF checks, escaped templates, and private downloads.
- [x] Generate a Supabase migration via CLI, add private bucket and RLS policies,
  and document setup plus a two-account isolation check.
- [x] Create Docker and Railway configuration and `.github/workflows/ci-cd.yml`.
  Require checks before the opt-in Railway job; include an image smoke test.
- [x] Write README, module/file placement guide, deployment guide, and contributor
  instructions; update the concept's stack and implementation links.
- [x] Run `composer validate --strict`, `composer check`, `composer audit`, and
  HTTP smoke checks. Report which checks require unavailable external services.

No Git repository exists initially; do not create commits or a remote.
No existing Supabase project, SMTP credentials, or Railway project is supplied.

## Verification result

- Composer validation, PHP syntax, PSR-12, and PHPUnit pass: 23 tests, 50 assertions.
- Composer audit reports no known dependency vulnerabilities.
- HTTP smoke checks pass for health, sign-in, guest redirects, method restrictions,
  CSRF rejection, private/configuration-path isolation, browser assets, and cookies.
- Workflow YAML, Supabase TOML, module directory structure, and local Markdown
  links were checked. A separate code review found and resolved the missing
  Docker archive extraction dependency; no significant findings remain.
- Docker image execution and pgTAP were not run locally because Docker is not
  installed. They are configured as required GitHub Actions jobs. Real upload
  isolation, SMTP delivery, and Railway deployment still require configured
  external services and credentials.
