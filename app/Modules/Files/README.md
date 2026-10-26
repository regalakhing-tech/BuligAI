# Files module

Owns private uploads, paginated listing, authenticated downloads, and deletion.

- `Controllers/FileController.php`: HTTP requests, verified user, views, download response.
- `Services/FileService.php`: Storage API access and owner-scoped object paths.
- `Services/UploadValidator.php`: actual MIME type and size validation (3 MiB on Vercel, 10 MiB locally).
- `Views/index.php`: upload form, listing, pagination, and file actions.
- `Requests/`: add reusable file request objects here if more file workflows need them.
- `Repositories/`: reserved for future database file metadata; Storage handles metadata today.

Uses the user's JWT and a publishable key. Never replace these with a service key.
The bucket is private. Downloads use signed attachment URLs valid for 60 seconds
to avoid proxying binary responses through Vercel. The recipient can use a signed
URL until it expires. Permanent public sharing, renaming, and file replacement
are outside this version. See [Supabase setup](../../../docs/SUPABASE.md).
