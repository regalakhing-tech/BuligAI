begin;
create extension if not exists pgtap with schema extensions;
set local search_path = public, extensions;
select plan(10);

select is((select public from storage.buckets where id = 'community-files'), false, 'bucket is private');
select is(
    (select file_size_limit from storage.buckets where id = 'community-files'),
    10485760::bigint,
    'bucket limits file size to ten MiB'
);

-- Metadata only, inside this rolled-back transaction; no objects are uploaded.
insert into storage.objects (bucket_id, name, owner_id)
values
    ('community-files', '11111111-1111-4111-8111-111111111111/a-guide.pdf', '11111111-1111-4111-8111-111111111111'),
    ('community-files', '22222222-2222-4222-8222-222222222222/b-guide.pdf', '22222222-2222-4222-8222-222222222222');

set local role authenticated;
select set_config('request.jwt.claims', '{"sub":"11111111-1111-4111-8111-111111111111","role":"authenticated"}', true);
select is((select count(*) from storage.objects where bucket_id = 'community-files'), 1::bigint, 'A sees only A files');
-- Test-only metadata deletes are permitted in this rolled-back transaction.
set local storage.allow_delete_query = 'true';
with removed as (
    delete from storage.objects
    where bucket_id = 'community-files' and name like '22222222-%'
    returning id
)
select is(
    (select count(*) from removed),
    0::bigint,
    'A cannot delete B files'
);
select throws_ok(
    $$insert into storage.objects (bucket_id, name, owner_id) values
    ('community-files', '22222222-2222-4222-8222-222222222222/forged.pdf', '11111111-1111-4111-8111-111111111111')$$,
    '42501',
    'new row violates row-level security policy for table "objects"',
    'A cannot upload to B folder'
);
with changed as (
    update storage.objects set name = '11111111-1111-4111-8111-111111111111/replaced.pdf'
    where bucket_id = 'community-files'
    returning id
)
select is(
    (select count(*) from changed),
    0::bigint,
    'replacement is disabled'
);
select lives_ok(
    $$insert into storage.objects (bucket_id, name, owner_id) values
    ('community-files', '11111111-1111-4111-8111-111111111111/own.pdf', '11111111-1111-4111-8111-111111111111')$$,
    'A can upload to A folder'
);
with removed as (
    delete from storage.objects
    where bucket_id = 'community-files' and name = '11111111-1111-4111-8111-111111111111/own.pdf'
    returning id
)
select is((select count(*) from removed), 1::bigint, 'A can delete A file');
select set_config('request.jwt.claims', '{"sub":"22222222-2222-4222-8222-222222222222","role":"authenticated"}', true);
select is((select count(*) from storage.objects where bucket_id = 'community-files'), 1::bigint, 'B sees only B files');
set local role anon;
select set_config('request.jwt.claims', '{"role":"anon"}', true);
select is((select count(*) from storage.objects where bucket_id = 'community-files'), 0::bigint, 'guests see no files');

select * from finish();
rollback;
