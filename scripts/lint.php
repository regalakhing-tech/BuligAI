<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$count = 0;
foreach (['api', 'app', 'bootstrap', 'config', 'public', 'routes', 'scripts', 'tests'] as $directory) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/' . $directory));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $output = [];
            exec(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($file->getPathname()) . ' 2>&1', $output, $code);
            if ($code !== 0) {
                fwrite(STDERR, implode(PHP_EOL, $output) . PHP_EOL);
                exit($code);
            }
            $count++;
        }
    }
}
echo "PHP syntax valid: {$count} files." . PHP_EOL;
