<div align="center">

# BuligAI

### Community knowledge. Clearer government services.

**Built for the people of Antique, Philippines.**

*Bulig sang komunidad, gabay sang teknolohiya.*

![PHP 8.5](https://img.shields.io/badge/PHP-8.5-777BB4?logo=php&logoColor=white)
![Supabase](https://img.shields.io/badge/Supabase-Auth%20%2B%20Storage-3ECF8E?logo=supabase&logoColor=white)
![PHPMailer 7](https://img.shields.io/badge/PHPMailer-7-216449)
![GitHub Actions](https://img.shields.io/badge/CI%2FCD-GitHub%20Actions-2088FF?logo=githubactions&logoColor=white)
[![CI checks](https://github.com/regalakhing-tech/BuligAI/actions/workflows/ci-cd.yml/badge.svg)](https://github.com/regalakhing-tech/BuligAI/actions/workflows/ci-cd.yml)

[Get started](#get-started) · [Project structure](#project-structure) · [Module guide](docs/FILE_MANAGEMENT.md) · [Deployment](docs/DEPLOYMENT.md) · [Source](https://github.com/regalakhing-tech/BuligAI)

</div>

---

BuligAI brings together community experiences, government service guides, and
public office locations so Antiqueños can help one another find practical
information. This repository establishes the PHP foundation for that platform,
including a private file workspace.

## What works today

- Email/password sign-in for existing Supabase users, session refresh, and logout.
- Private file uploads, paginated listing, downloads, and deletion.
- PDF, JPEG, PNG, and WebP validation based on file content; **3 MiB on Vercel**, 10 MiB locally.
- Supabase Storage policies that limit files to their owner.
- PHPMailer SMTP service and an explicit test-email command.
- Automated PHP checks, HTTP/session checks, storage policy tests, and an optional Vercel deployment job.

Community guides, maps, AI answers, discussions, and moderation have dedicated
module folders and implementation guidance. Their application features are planned.

## Technology

| Layer | Technology |
| --- | --- |
| Runtime | PHP **8.5**, the current stable series |
| Dependencies | Composer 2, PSR-4 autoloading, committed `composer.lock` |
| Identity | Supabase Auth through its REST API |
| Database and files | Supabase PostgreSQL and private Storage |
| HTTP integration | Guzzle 7 |
| Application email | PHPMailer 7 over SMTP with TLS |
| Interface | Server-rendered PHP, HTML, CSS, and JavaScript |
| Verification | PHPUnit 12, PHP_CodeSniffer / PSR-12, pgTAP |
| Delivery | GitHub Actions and Vercel's community PHP runtime |
| Production sessions | Encrypted, authenticated HttpOnly cookies; no shared disk required |
| Planned features | Gemini API, Leaflet / OpenStreetMap, Tailwind CSS |

PHP 8.5.12 was the latest stable patch checked on October 26, 2026. Install the
latest available 8.5 patch; CI and the Vercel runtime target that series.
See the [official PHP release feed](https://www.php.net/releases/index.php?json&version=8.5).

## Get started

**Requirements:** PHP 8.5, Composer 2, and a Supabase project. Enable `curl`,
`fileinfo`, `mbstring`, and `openssl`; tests also need `dom`, `xml`, and `xmlwriter`.

From the project directory:

```powershell
composer install
Copy-Item .env.example .env
```

For a fresh checkout, first run `git clone https://github.com/regalakhing-tech/BuligAI.git`
and `cd BuligAI`. The full [project concept](BuligAI_Project_Concept.md) describes the roadmap.

Set these values in `.env` using your project's **publishable** API key:

```dotenv
SUPABASE_URL=https://your-project.supabase.co
SUPABASE_PUBLISHABLE_KEY=sb_publishable_your_key
SUPABASE_STORAGE_BUCKET=community-files
```

The starter intentionally accepts publishable keys only. Supabase documents these
in its [API key guide](https://supabase.com/docs/guides/getting-started/api-keys).
Storage requests run with the signed-in user's access token. Locally, PHP stores
session state in files. On Vercel, state is encrypted in an HttpOnly cookie using
a server-only `APP_KEY`; browser JavaScript cannot read the tokens.

Before uploading, apply the supplied storage migration as described in
[Supabase setup](docs/SUPABASE.md), then create an email/password user in
**Supabase Dashboard → Authentication → Users**. The starter provides sign-in;
public registration and password-reset screens are future Auth module work.

```powershell
composer serve
```

Open **http://localhost:8000/login**. The sign-in page and `/health` can load
before external credentials are configured. `composer serve` allows local uploads
up to 10 MiB. To start PHP directly with those local limits:

```powershell
php -d upload_max_filesize=10M -d post_max_size=11M -S localhost:8000 -t public public/router.php
```

On macOS or Linux, use `cp .env.example .env` instead of `Copy-Item`.

## Project structure

```text
BuligAI/
├── app/
│   ├── Modules/                  # Business features, grouped by module
│   │   ├── Auth/                 # Sign-in, session refresh, sign-out
│   │   ├── Files/                # Private upload, listing, download, delete
│   │   ├── Notifications/        # Application email
│   │   ├── Guides/               # Government service guides
│   │   ├── Community/            # Community posts, comments, experiences
│   │   ├── Locations/            # Public offices, map pins, directions
│   │   ├── Assistant/            # Grounded AI answers and source links
│   │   ├── Questions/            # Community Q&A and helpful votes
│   │   ├── Checklists/           # Personal requirements checklists
│   │   ├── Moderation/           # Reports, corrections, review status
│   │   └── Municipalities/       # Municipality-based discovery
│   └── Shared/                   # HTTP, security, Supabase, mail, layouts
├── api/                          # Vercel PHP entry point and PHP settings
├── bootstrap/                    # Autoload and environment initialization
├── config/                       # Configuration derived from environment
├── routes/                       # Method + URL → module controller
├── public/                       # The ONLY web document root
│   └── assets/                   # CSS, JavaScript, public brand assets
├── supabase/
│   ├── migrations/               # Versioned database and Storage policies
│   └── tests/                    # Transactional pgTAP policy tests
├── storage/                      # Local development session/temp data
├── tests/                        # PHP unit and feature tests
├── scripts/                      # Development and maintenance commands
├── docs/                         # Setup, placement, deployment, design notes
├── .github/workflows/ci-cd.yml    # Checks → image → optional deploy
├── .env.example                  # Copyable configuration, no credentials
├── composer.json
├── composer.lock
├── package.json                  # Node 22 metadata for the PHP runtime wrapper
└── vercel.json                   # PHP 8.5 function and public asset routes
```

**Where should a new file go?** Use [the module-by-module file placement guide](docs/FILE_MANAGEMENT.md).
Module folders include `Controllers`, `Services`, `Repositories`, `Requests`, and
`Views` so contributors have an obvious location for each responsibility.

## File workspace

| Action | Route |
| --- | --- |
| Sign in | `GET /login`, `POST /login` |
| Browse files | `GET /files?page=1` |
| Upload | `POST /files` with multipart field `file` |
| Download | `GET /files/download?path=...` |
| Delete | `POST /files/delete` |
| Sign out | `POST /logout` |
| Liveness | `GET /health` |

All POST forms require a CSRF token. Filenames receive a random prefix and a
content-derived extension. Each user's objects live under
`community-files/{user-uuid}/{random-prefix}-{safe-name}.pdf` (or an allowed image
extension). Downloads require a verified session and redirect to a Supabase
attachment URL valid for 60 seconds. Anyone holding that temporary link can use
it until expiry. Duplicate uploads receive distinct names; file replacement and
permanent public links are disabled.

Files live in Supabase Storage, outside the PHP web root. Use public reference
materials rather than IDs or sensitive application records. `/health` confirms
the PHP application is running; it does not check Supabase or SMTP connectivity.

## Email with PHPMailer

Set your provider's SMTP details in `.env`:

```dotenv
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="BuligAI Community"
```

Use `ssl` and port `465` if your provider requires implicit TLS. Confirm the sender
address with your provider. To deliberately send one test email:

```powershell
composer mail:test -- your-address@example.com
```

The reusable sender is `app/Modules/Notifications/Services/Mailer.php`.
[PHPMailer](https://github.com/PHPMailer/PHPMailer) handles application messages.
Configure [Supabase Auth SMTP](https://supabase.com/docs/guides/auth/auth-smtp)
separately for verification, recovery, and invitation emails.

## Checks and CI/CD

```powershell
composer validate --strict
composer check
composer audit --locked
```

`composer check` runs PHP syntax checks, PSR-12 checks, and PHPUnit. Tests use
simulated HTTP responses for Supabase and do not send emails or require live keys.
The separate storage policy tests run against a disposable local Supabase stack
in GitHub Actions. See [Supabase setup](docs/SUPABASE.md) for running them locally.

The [workflow](.github/workflows/ci-cd.yml) runs for pull requests and pushes to
`main` / `develop`. It smoke-tests the PHP entry point with both session drivers.
Deployment requires all three check jobs to pass, a push or manual run on `main`, and the repository
variable `DEPLOY_ENABLED=true`.

## Deploy to Vercel

The project uses **`vercel-php@0.9.0`**, a community PHP 8.5 runtime listed in
[Vercel's runtime documentation](https://vercel.com/docs/functions/runtimes).
No application Dockerfile or container hosting is required.

1. Create or link a Vercel project from this repository, using **Other** as the
   framework, the repository root, and Node **22.x**. Leave Build Command empty;
   `vercel.json` sets static output to `public`, and the runtime installs Composer.
2. Add your Supabase and `MAIL_*` settings to Vercel's environment variables.
3. Generate a session encryption key locally and save it as server-only `APP_KEY`:

   ```powershell
   php -r 'echo base64_encode(random_bytes(32)), PHP_EOL;'
   ```

   Set `APP_ENV=production`, `SESSION_DRIVER=cookie`, and `APP_URL` to your HTTPS
   domain. Keep `APP_KEY` stable across deployments. Vercel automatically selects
   encrypted cookies even if a file-session setting was copied accidentally.
4. Follow the [deployment guide](docs/DEPLOYMENT.md) to add GitHub's
   `VERCEL_TOKEN`, `VERCEL_ORG_ID`, and `VERCEL_PROJECT_ID` secrets, then enable
   `DEPLOY_ENABLED=true`. The workflow builds once and deploys the checked output.

Vercel's [4.5 MB request/response limit](https://vercel.com/docs/functions/limitations)
means PHP-proxied uploads are capped at **3 MiB** there, with room for multipart
overhead. Downloads go directly to Supabase. The bucket and local development
retain their 10 MiB limit. Larger browser uploads would require a separate direct
Storage upload flow, which this version does not implement.

Docker is needed only if you run the local Supabase SQL policy tests; their CI job
uses an isolated local stack. Hosted Supabase and the deployed app need no local Docker.
Installing the project does not deploy it or apply a live database migration.

## Contributing

Read [CONTRIBUTING.md](CONTRIBUTING.md), keep changes within their owning module,
and run the checks before opening a pull request. Information contributed to the
future community platform should distinguish personal experience from official
government sources.

The concept describes an open-source project. A source-code license has not yet
been selected; choose and add one before a public open-source release. Composer's
`proprietary` value records the current unlicensed status.

---

**Built by the community. Powered by shared knowledge. Made for Antiqueños.**
