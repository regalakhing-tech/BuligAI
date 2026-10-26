# Supabase setup and private files

## Hosted project

1. Create or select a development Supabase project.
2. Copy its HTTPS project URL and **publishable key** to `.env`.
3. Review the generated migration in `supabase/migrations` before applying it.
4. Apply the migration using the official CLI, or run its SQL once through the
   project's SQL Editor if you do not use CLI migrations yet. Do not apply it both ways.
5. Create an email/password user in Authentication → Users, then sign in at `/login`.

CLI commands, with your Supabase project reference substituted:

```text
supabase --version
supabase login
supabase link --project-ref YOUR_PROJECT_REF
supabase db push --dry-run
supabase db push
```

Use the CLI's `--help` if your installed version differs. The local configuration
and CI use CLI **2.122.0**. On Windows, use `npx.cmd --yes supabase@2.122.0` in place
of `supabase` if PowerShell blocks npm's `.ps1` launcher.

The migration creates or configures `community-files` as private, limits uploads
to 10 MiB and the four supported MIME types, and adds INSERT, SELECT, and DELETE
policies. Both the UUID folder and `owner_id` must match `auth.uid()`. No UPDATE
policy is granted. If changing the bucket name, update the migration's bucket
checks and `SUPABASE_STORAGE_BUCKET` together before applying to a fresh project.
For an already migrated project, create a new migration instead.

Use a dedicated project or review existing `storage.objects` policies first:
permissive policies combine with OR, so an older broad policy can override the
intended isolation. Never make this bucket public. The design follows
[Storage access control](https://supabase.com/docs/guides/storage/security/access-control)
and [object ownership](https://supabase.com/docs/guides/storage/security/ownership).

## Verify the real integration

Use two disposable accounts, A and B, in your development project:

1. Sign in as A and upload a small PDF and an image. Check listing and downloads.
2. Copy A's application URL (`/files/download?path=...`). Sign out, sign in as B,
   and try that URL: access must be denied. The final Supabase signed URL is a
   temporary bearer link and remains usable by its holder until its 60-second expiry.
3. B's list must not show A's files. B must not be able to delete A's object path.
4. Sign in as A again; delete A's file and confirm it disappears from Storage.
5. Try a PHP/text executable renamed `.jpg`, an empty file, and a file over the
   displayed limit (3 MiB on Vercel, 10 MiB locally).
   Each upload must be rejected.
6. Check the bucket remains private and that a public object URL does not work.

This checks the PHP transport, actual Auth settings, Storage API, and live RLS
together. Unit tests use simulated responses and cannot prove a hosted project's
policies have been applied.

## Local database policy tests

Install Docker and the official Supabase CLI. From this repository:

```text
supabase start
supabase test db
supabase stop
```

`start` applies the versioned migration in a fresh local stack. The pgTAP test
creates transactional metadata fixtures, verifies private bucket settings,
own-user access, cross-user denial, and disabled replacement, then rolls back.
Test-only direct metadata deletes enable `storage.allow_delete_query` inside
that transaction. Application deletion always uses the Storage API.

The PHP application currently targets a hosted HTTPS project with a publishable
key. The local Supabase stack is used for SQL policy tests; its default HTTP URL
and legacy keys are not accepted by the PHP adapter.

## Vercel application hosting

Supabase remains responsible for Auth, PostgreSQL, and persistent files. Vercel
runs PHP with the community runtime; it does not run the local Supabase stack.
Follow [deployment setup](DEPLOYMENT.md), including `APP_KEY` for encrypted
serverless session state. The Vercel upload limit is lower than the bucket limit
because uploads pass through a Vercel function. Signed downloads go directly to
Supabase and expire after 60 seconds.

## Future database modules

Create migrations with `supabase migration new descriptive_name`. Keep each
module's database access in its repository. Enable RLS for every new exposed
table and add explicit role grants when exposing it through the Data API.
Test ownership and moderation permissions. User-editable metadata must never
determine moderator privileges.

The initial application has no direct Postgres connection and no invented
community tables. Add those tables when implementing their owning modules.
