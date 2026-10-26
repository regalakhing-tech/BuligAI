<?php

declare(strict_types=1);

namespace BuligAI\Shared\Http;

use BuligAI\Modules\Auth\Controllers\AuthController;
use BuligAI\Modules\Auth\Services\AuthService;
use BuligAI\Modules\Files\Controllers\FileController;
use BuligAI\Modules\Files\Services\FileService;
use BuligAI\Modules\Files\Services\UploadValidator;
use BuligAI\Shared\Security\Csrf;
use BuligAI\Shared\Supabase\ApiException;
use BuligAI\Shared\Supabase\SupabaseClient;
use InvalidArgumentException;
use Throwable;

final class Application
{
    public function __construct(private readonly array $config)
    {
    }

    public function handle(string $method, string $path): Response
    {
        $routes = require dirname(__DIR__, 3) . '/routes/web.php';
        if (!isset($routes[$path])) {
            return $this->error('Page not found.', 404);
        }
        if (!isset($routes[$path][$method])) {
            return new Response('Method not allowed.', 405, [
                'Content-Type' => 'text/plain; charset=utf-8',
                'Allow' => implode(', ', array_keys($routes[$path])),
            ]);
        }
        try {
            if ($method === 'POST') {
                $maxBytes = $this->config['upload_max_bytes'] ?? UploadValidator::MAX_BYTES;
                if ((int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > $maxBytes + 1024 * 1024) {
                    return $this->error('The upload is too large. Choose a file within the displayed limit.', 413);
                }
                Csrf::verify($_SESSION, (string) ($_POST['_csrf'] ?? ''));
            }
            $action = $routes[$path][$method];
            if ($action === 'health') {
                return new Response('{"status":"ok","app":"BuligAI"}', 200, ['Content-Type' => 'application/json']);
            }
            if ($action === 'home') {
                return Response::redirect('/files');
            }
            if ($action === 'loginForm') {
                return new Response(View::render('Modules/Auth/Views/login', [
                    'csrf' => Csrf::token($_SESSION), 'title' => 'Sign in',
                ]));
            }
            if (str_starts_with($action, 'files.') && empty($_SESSION['auth'])) {
                return Response::redirect('/login');
            }
            $client = new SupabaseClient($this->config['supabase']['url'], $this->config['supabase']['key']);
            $auth = new AuthService($client);
            if ($action === 'login' || $action === 'logout') {
                return (new AuthController($auth))->{$action}();
            }
            $maxBytes = $this->config['upload_max_bytes'] ?? UploadValidator::MAX_BYTES;
            $controller = new FileController($auth, new FileService(
                $client,
                new UploadValidator($maxBytes),
                $this->config['supabase']['bucket']
            ), $maxBytes);
            return $controller->{substr($action, 6)}();
        } catch (ApiException $error) {
            if (in_array($error->status, [400, 401], true)) {
                unset($_SESSION['auth']);
                return $this->error('Please sign in again. Check your email and password.', 401);
            }
            if ($error->status === 404) {
                return $this->error('File or storage bucket not found.', 404);
            }
            if ($error->status === 403) {
                return $this->error('You do not have access to this file.', 403);
            }
            if ($error->status === 429) {
                return $this->error('Too many requests. Please try again shortly.', 429);
            }
            error_log('BuligAI: upstream HTTP ' . $error->status);
            return $this->error('The service is temporarily unavailable. Please try again.', 502);
        } catch (InvalidArgumentException $error) {
            return $this->error($error->getMessage(), 422);
        } catch (Throwable $error) {
            error_log('BuligAI: ' . $error::class);
            return $this->error('Something went wrong. Please try again.', 500);
        }
    }

    private function error(string $message, int $status): Response
    {
        return new Response(View::render('Shared/Views/error', [
            'title' => 'Request could not be completed', 'message' => $message,
        ]), $status);
    }
}
