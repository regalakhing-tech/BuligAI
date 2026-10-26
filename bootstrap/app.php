<?php

declare(strict_types=1);

use BuligAI\Shared\Http\Application;
use Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

return new Application(require dirname(__DIR__) . '/config/app.php');
