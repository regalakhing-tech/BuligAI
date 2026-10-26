<?php

declare(strict_types=1);

namespace BuligAI\Modules\Files\Services;

use finfo;
use InvalidArgumentException;

final class UploadValidator
{
    public const MAX_BYTES = 10 * 1024 * 1024;
    private const TYPES = [
        'application/pdf' => 'pdf',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    public function __construct(private readonly int $maxBytes = self::MAX_BYTES)
    {
    }

    public function validate(string $path, int $error): array
    {
        if ($error !== UPLOAD_ERR_OK || !is_file($path) || !is_readable($path)) {
            throw new InvalidArgumentException('Upload did not complete. Choose a file within the displayed limit.');
        }
        $size = filesize($path);
        if ($size === false || $size < 1 || $size > $this->maxBytes) {
            $limit = (int) ($this->maxBytes / 1024 / 1024);
            throw new InvalidArgumentException("Files must be non-empty and no larger than {$limit} MiB.");
        }
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($path);
        if (!isset(self::TYPES[$mime])) {
            throw new InvalidArgumentException('Choose a PDF, JPEG, PNG, or WebP file.');
        }
        return ['mime' => $mime, 'extension' => self::TYPES[$mime], 'size' => $size];
    }
}
