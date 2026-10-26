<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e($title ?? 'Community workspace') ?> · BuligAI</title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
    <header class="site-header">
        <a class="brand" href="/files" aria-label="BuligAI workspace">bulig<span>ai</span><span class="brand-dot">.</span></a>
        <span class="place">ANTIQUE, PHILIPPINES</span>
    </header>
    <main><?= $content ?></main>
    <footer>Bulig sang komunidad, gabay sang teknolohiya.<span>Made for Antiqueños.</span></footer>
    <script src="/assets/js/app.js" defer></script>
</body>
</html>
