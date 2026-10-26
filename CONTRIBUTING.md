# Contributing to BuligAI

Start with [the concept](BuligAI_Project_Concept.md) and
[file placement guide](docs/FILE_MANAGEMENT.md). Keep a change within its module
where possible, and describe the observable behavior in the pull request.

1. Install PHP 8.5 and run `composer install`.
2. Copy `.env.example` to `.env`; keep credentials out of commits.
3. Create a feature branch in your Git repository.
4. Write focused behavior tests for new workflows or authorization rules.
5. Add schema changes as new Supabase migrations and include policy tests.
6. Run `composer validate --strict`, `composer check`, and `composer audit --locked`.
7. Explain the change and validation in your pull request.

Use PSR-12 and PSR-4. `composer style:fix` fixes supported formatting issues.
Commit `composer.lock` after intentional dependency updates; do not commit
`vendor`, session files, uploads, `.env`, or generated caches.

Community content should cite sources where possible and distinguish resident
experience, community review, and official-source support. Do not describe a
community review as government approval.

Before a public open-source release, the maintainer must select a source-code
license. The current repository does not grant an open-source license.
