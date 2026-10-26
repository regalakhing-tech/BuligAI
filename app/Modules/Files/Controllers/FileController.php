<?php

declare(strict_types=1);

namespace BuligAI\Modules\Files\Controllers;

use BuligAI\Modules\Auth\Services\AuthService;
use BuligAI\Modules\Files\Services\FileService;
use BuligAI\Modules\Files\Services\UploadValidator;
use BuligAI\Shared\Http\Response;
use BuligAI\Shared\Http\View;
use BuligAI\Shared\Security\Csrf;
use InvalidArgumentException;

final class FileController
{
    public function __construct(
        private readonly AuthService $auth,
        private readonly FileService $files,
        private readonly int $maxUploadBytes = UploadValidator::MAX_BYTES
    ) {
    }

    public function index(): Response
    {
        $user = $this->auth->user($_SESSION['auth']);
        $page = max(1, min((int) ($_GET['page'] ?? 1), 100000));
        $items = $this->files->list($user['id'], $_SESSION['auth']['access_token'], $page);
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return new Response(View::render('Modules/Files/Views/index', [
            'title' => 'My files', 'user' => $user, 'files' => $items, 'page' => $page,
            'csrf' => Csrf::token($_SESSION), 'flash' => $flash,
            'maxUploadMiB' => (int) ($this->maxUploadBytes / 1024 / 1024),
        ]));
    }

    public function upload(): Response
    {
        $user = $this->auth->user($_SESSION['auth']);
        $file = $_FILES['file'] ?? [];
        if (!is_string($file['tmp_name'] ?? null) || !is_uploaded_file($file['tmp_name'])) {
            throw new InvalidArgumentException('Choose a PDF or image file within the displayed limit.');
        }
        $this->files->upload(
            $user['id'],
            $_SESSION['auth']['access_token'],
            $file['tmp_name'],
            (string) $file['name'],
            (int) $file['error']
        );
        $_SESSION['flash'] = 'File uploaded to your private workspace.';
        return Response::redirect('/files');
    }

    public function download(): Response
    {
        $user = $this->auth->user($_SESSION['auth']);
        $object = (string) ($_GET['path'] ?? '');
        $url = $this->files->downloadUrl($user['id'], $_SESSION['auth']['access_token'], $object);
        return new Response('', 303, [
            'Location' => $url,
            'Cache-Control' => 'private, no-store',
        ]);
    }

    public function delete(): Response
    {
        $user = $this->auth->user($_SESSION['auth']);
        $this->files->delete($user['id'], $_SESSION['auth']['access_token'], (string) ($_POST['path'] ?? ''));
        $_SESSION['flash'] = 'File deleted.';
        return Response::redirect('/files');
    }
}
