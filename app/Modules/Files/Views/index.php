<section class="workspace">
    <div class="workspace-heading">
        <div><p class="eyebrow">YOUR PRIVATE WORKSPACE</p><h1>My files<span class="green">.</span></h1></div>
        <form method="post" action="/logout">
            <input type="hidden" name="_csrf" value="<?= $e($csrf) ?>">
            <span class="account"><?= $e($user['email'] ?? 'Signed in') ?></span>
            <button type="submit" class="button-quiet">Sign out</button>
        </form>
    </div>
    <p class="muted">Upload useful guides and reference images. Only you can access these files.</p>
    <?php if ($flash): ?><p class="notice" role="status"><?= $e($flash) ?></p><?php endif; ?>
    <form method="post" action="/files" enctype="multipart/form-data" class="upload-form">
        <input type="hidden" name="_csrf" value="<?= $e($csrf) ?>">
        <div>
            <label for="file">Add a file</label>
            <p class="small muted" id="file-help">PDF, JPEG, PNG, or WebP · up to <?= $e($maxUploadMiB) ?> MiB</p>
            <input id="file" name="file" type="file" accept=".pdf,.jpg,.jpeg,.png,.webp" aria-describedby="file-help" required>
        </div>
        <button type="submit">Upload file <span aria-hidden="true">↑</span></button>
    </form>
    <p class="small muted">Use public reference materials. Avoid uploading IDs, medical records, or other sensitive personal documents.</p>
    <div class="table-heading"><h2>Saved files</h2><span class="small muted">Page <?= $e($page) ?></span></div>
    <?php if (!$files): ?>
        <div class="empty-state"><p>No files here yet.</p><span class="muted">Your uploaded files will appear here.</span></div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <caption class="sr-only">Your private uploaded files</caption>
                <thead><tr><th scope="col">File name</th><th scope="col">Size</th><th scope="col">Uploaded</th><th scope="col">Actions</th></tr></thead>
                <tbody>
                <?php foreach ($files as $file): ?>
                    <?php
                    $object = $user['id'] . '/' . $file['name'];
                    $displayName = preg_replace('/^[a-f0-9]{32}-/', '', $file['name']);
                    ?>
                    <tr>
                        <td class="file-name"><?= $e($displayName) ?></td>
                        <td><?= $e(number_format(($file['metadata']['size'] ?? 0) / 1024, 1)) ?> KiB</td>
                        <td><?= $e(substr($file['created_at'] ?? '', 0, 10)) ?></td>
                        <td><div class="file-actions">
                            <a href="/files/download?path=<?= $e(rawurlencode($object)) ?>" aria-label="Download <?= $e($displayName) ?>">Download</a>
                            <form method="post" action="/files/delete" data-confirm-delete>
                                <input type="hidden" name="_csrf" value="<?= $e($csrf) ?>">
                                <input type="hidden" name="path" value="<?= $e($object) ?>">
                                <button type="submit" class="button-quiet" aria-label="Delete <?= $e($displayName) ?>">Delete</button>
                            </form>
                        </div></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    <nav class="pagination" aria-label="File pages">
        <?php if ($page > 1): ?><a href="/files?page=<?= $e($page - 1) ?>">← Previous</a><?php endif; ?>
        <?php if (count($files) === 100): ?><a href="/files?page=<?= $e($page + 1) ?>">Next →</a><?php endif; ?>
    </nav>
</section>
