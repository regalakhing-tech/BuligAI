-- Private reference files; object data is managed exclusively through Storage API.
insert into storage.buckets (id, name, public, file_size_limit, allowed_mime_types)
values (
    'community-files', 'community-files', false, 10485760,
    array['application/pdf', 'image/jpeg', 'image/png', 'image/webp']
)
on conflict (id) do update set
    public = excluded.public,
    file_size_limit = excluded.file_size_limit,
    allowed_mime_types = excluded.allowed_mime_types;

-- storage.objects already has RLS enabled by Supabase. Never use a service key
-- for these requests, because service keys bypass this authorization boundary.
create policy "buligai_files_insert_own"
on storage.objects for insert to authenticated
with check (
    bucket_id = 'community-files'
    and (storage.foldername(name))[1] = (select auth.uid())::text
    and owner_id = (select auth.uid())::text
);

create policy "buligai_files_select_own"
on storage.objects for select to authenticated
using (
    bucket_id = 'community-files'
    and (storage.foldername(name))[1] = (select auth.uid())::text
    and owner_id = (select auth.uid())::text
);

create policy "buligai_files_delete_own"
on storage.objects for delete to authenticated
using (
    bucket_id = 'community-files'
    and (storage.foldername(name))[1] = (select auth.uid())::text
    and owner_id = (select auth.uid())::text
);

-- No UPDATE policy: every upload gets a new name; replacement is disabled.
