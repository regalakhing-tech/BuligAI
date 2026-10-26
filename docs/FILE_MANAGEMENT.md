# Where to put your files

BuligAI uses a **modular monolith**: one PHP application, with each feature's
files kept together. This guide covers source-code organization and the separate
user file workspace.

## The standard module layout

```text
app/Modules/Guides/
├── Controllers/GuideController.php     # Reads request; calls service; returns response
├── Services/GuideService.php           # Feature rules and workflows
├── Repositories/GuideRepository.php    # Supabase queries and persistence
├── Requests/CreateGuideRequest.php     # Validates and normalizes submitted fields
├── Views/index.php                     # Escaped HTML for a list of guides
├── Views/show.php                      # Escaped HTML for one guide
└── README.md                           # Module ownership and implementation status
```

The PHP files in this example show **where future guide code belongs**; they are
not fabricated working features. Empty directories are tracked with `.gitkeep`.
Remove a directory's `.gitkeep` when it gains real files. Only create a class when
it has a responsibility; a simple module need not use every layer.

## Module-by-module ownership

| Module | Put these files here | Current status |
| --- | --- | --- |
| `Auth` | Login/logout controllers, session services, registration and recovery requests, auth views | Sign-in, refresh, logout implemented |
| `Files` | Upload controller, MIME validation, private Storage operations, file workspace views | Upload/list/download/delete implemented |
| `Notifications` | Email delivery workflows and future notification templates | PHPMailer sender implemented |
| `Guides` | Service requirements, guide steps, categories, reference links | Folder structure prepared |
| `Community` | Posts, comments, resident experiences, community feed | Folder structure prepared |
| `Locations` | Public office locations, coordinates, map data, directions | Folder structure prepared |
| `Assistant` | Gemini integration, retrieval, answer grounding, source citations | Folder structure prepared |
| `Questions` | Questions, answers, helpful votes | Folder structure prepared |
| `Checklists` | Personal preparation lists and completion state | Folder structure prepared |
| `Moderation` | Reports, correction suggestions, review decisions, verification labels | Folder structure prepared |
| `Municipalities` | Municipality directory and location filters | Folder structure prepared |

## Shared files

| File or folder | Responsibility |
| --- | --- |
| `app/Shared/Http` | Application dispatch, HTTP responses, view rendering |
| `app/Shared/Security` | CSRF protection shared by forms |
| `app/Shared/Security/CookieSession.php` | Authenticated encryption of Vercel session cookies |
| `app/Shared/Security/SessionManager.php` | Cookie sessions on Vercel, file sessions locally |
| `app/Shared/Supabase` | Authenticated HTTP transport and safe upstream errors |
| `app/Shared/Mail` | PHPMailer construction and SMTP settings |
| `app/Shared/Views` | Shared page layout and error page |
| `routes/web.php` | Registers request methods, paths, and controller actions |
| `config/app.php` | Reads environment configuration; no hardcoded secrets |
| `bootstrap/app.php` | Loads Composer and environment; creates application |
| `api/index.php` | Vercel function entry point; loads the public application |
| `api/php.ini` | PHP settings for the Vercel community runtime |
| `vercel.json` | Function runtime, static asset allowlist, application routing |
| `public/assets/css` | Browser styles |
| `public/assets/js` | Browser interactions |
| `public/assets/images` | Public logos and non-sensitive images |
| `supabase/migrations` | Versioned SQL, including RLS policies |
| `supabase/tests` | Database authorization tests |
| `storage/tmp` | Local development PHP sessions; ignored by Git and Vercel uploads |
| `storage/logs` | Reserved local log directory; production logs go to stderr |
| `tests/Unit` | Service, validation, and infrastructure behavior tests |
| `tests/Feature` | Application request/response tests |
| `scripts` | Developer commands, syntax checks, explicit SMTP test |
| `.github/workflows` | CI/CD automation |
| `docs` | Architecture, setup, file placement, deployment instructions |

Keep business rules in the owning module. A controller should not contain SQL,
SMTP construction, or duplicate Supabase HTTP code. Modules use public service
interfaces rather than reaching into another module's controller or view.

## Naming conventions

- Folders and classes: `PascalCase`, e.g. `GuideService.php`.
- Class namespace matches the path: `BuligAI\Modules\Guides\Services`.
- Methods and variables: `camelCase`; constants: `UPPER_SNAKE_CASE`.
- View filenames: lowercase; browser assets: `kebab-case`.
- One class per file, `declare(strict_types=1)`, PSR-12 formatting.
- Database tables/columns: `snake_case`. Generate migration filenames with
  `supabase migration new descriptive_name`; do not rename applied migrations.
- Tests: end in `Test.php`, use a matching namespace, and describe observable behavior.

## Adding a module feature

1. Choose the module from the ownership table.
2. Write a behavior test under `tests/Unit` or `tests/Feature`.
3. Add validation in `Requests` when reusable, workflow rules in `Services`,
   persistence in `Repositories`, and request handling in `Controllers`.
4. Add escaped templates in the module's `Views` directory.
5. Register routes in `routes/web.php` and connect their controller in the shared
   application dispatcher; apply authorization and CSRF checks.
6. Generate a migration for persistent schema changes; enable RLS and explicitly
   grant only the necessary API roles. Add policy tests for other users' access.
7. Run `composer check`, update the module README, and document configuration changes.

## User-uploaded files

User files are a separate concern from source files. Store them in the private
Supabase `community-files` bucket under the authenticated user's UUID.
`Files/Services/FileService.php` is the single application boundary for storage.

Never save uploads in `public`, `app`, `vendor`, or a Git-tracked folder.
`storage/tmp` is for local runtime data, not persistent uploads. Do not add
`service_role` keys to file operations: they bypass ownership policies.
