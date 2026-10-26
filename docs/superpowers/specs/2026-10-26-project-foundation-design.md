# BuligAI project foundation

Historical foundation design. Its hosting/session sections are superseded by
[the Vercel adaptation](2026-10-27-vercel-deployment-design.md).

The approved scope is a modular PHP foundation with Supabase, PHPMailer,
GitHub Actions, documentation, and a user-facing file upload feature.

Use a modular monolith: each business feature owns its controllers, services,
repositories, requests, and views under `app/Modules`. Shared HTTP, security,
Supabase, and mail infrastructure lives under `app/Shared`. Composer uses PSR-4.
PHP targets the current stable 8.5 series. Dependencies are locked.

Deliver email/password sign-in for existing Supabase users and a private file
workspace. Users can upload, paginate, download, and delete their own files.
Accept PDF, JPEG, PNG, and WebP, verified by actual MIME type, up to 10 MiB.
Store objects in a private `community-files` bucket under an authenticated user
UUID and a randomized safe filename. Apply INSERT, SELECT, and DELETE RLS
policies; disallow replacement. PHP uses the publishable API key and the user's
access token, never a service-role key. Sessions stay server-side, refresh
expired tokens, use CSRF protection, and validate identities with Supabase.

PHPMailer provides SMTP notifications and an explicit CLI test command. It does
not replace Supabase Auth email delivery. No email is sent automatically.

The file workspace uses a quiet cream and green interface, readable tables,
clear upload limits, accessible forms, and restrained hover/focus transitions.
The concept's remaining community, map, AI, Q&A, checklist, municipality, and
moderation modules are documented extension points, not completed features.

CI validates Composer, lints PHP, checks PSR-12, runs behavior tests, audits
dependencies, builds the production image, and smoke-tests HTTP responses.
CD runs only after successful checks on main and only when Railway deployment
is explicitly enabled in repository configuration. Deployment secrets and
runtime credentials are external. Database migrations are a separate,
documented operation, not automatically applied to a live project.

Verify upload validation, ownership boundaries, Supabase request construction,
token refresh, CSRF behavior, SMTP configuration, and public HTTP responses.
Live Supabase isolation and deployment need a configured project and credentials;
include a two-user verification procedure and a local SQL policy test.
