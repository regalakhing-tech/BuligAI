<?php

declare(strict_types=1);

return [
    '/' => ['GET' => 'home'],
    '/health' => ['GET' => 'health'],
    '/login' => ['GET' => 'loginForm', 'POST' => 'login'],
    '/logout' => ['POST' => 'logout'],
    '/files' => ['GET' => 'files.index', 'POST' => 'files.upload'],
    '/files/download' => ['GET' => 'files.download'],
    '/files/delete' => ['POST' => 'files.delete'],
];
