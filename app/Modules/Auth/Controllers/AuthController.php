<?php

declare(strict_types=1);

namespace BuligAI\Modules\Auth\Controllers;

use BuligAI\Modules\Auth\Services\AuthService;
use BuligAI\Shared\Http\Response;
use BuligAI\Shared\Security\SessionManager;
use BuligAI\Shared\Supabase\ApiException;
use InvalidArgumentException;

final class AuthController
{
    public function __construct(private readonly AuthService $auth)
    {
    }

    public function login(): Response
    {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '' || strlen($password) > 4096) {
            throw new InvalidArgumentException('Enter your email address and password.');
        }
        $session = $this->auth->signIn($email, $password);
        SessionManager::rotate();
        $_SESSION = ['auth' => $session];
        return Response::redirect('/files');
    }

    public function logout(): Response
    {
        try {
            if (!empty($_SESSION['auth']['access_token'])) {
                $this->auth->signOutSession($_SESSION['auth']);
            }
        } catch (ApiException $error) {
            error_log('BuligAI: remote sign-out HTTP ' . $error->status);
        } finally {
            $_SESSION = [];
            SessionManager::rotate();
        }
        return Response::redirect('/login');
    }
}
