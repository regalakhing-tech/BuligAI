<?php

declare(strict_types=1);

namespace BuligAI\Shared\Security;

use InvalidArgumentException;

final class SessionManager
{
    private ?CookieSession $codec = null;
    private string $cookieName = 'buligai_state';

    public function __construct(private readonly array $config, private readonly bool $production)
    {
        if (!in_array($config['driver'], ['file', 'cookie'], true)) {
            throw new InvalidArgumentException('SESSION_DRIVER must be file or cookie.');
        }
        if ($config['driver'] === 'cookie') {
            $this->codec = new CookieSession($config['key']);
            $this->cookieName = $production ? '__Host-buligai_state' : 'buligai_state';
        }
    }

    public function start(): void
    {
        if ($this->codec !== null) {
            $_SESSION = $this->codec->decode((string) ($_COOKIE[$this->cookieName] ?? ''));
            return;
        }
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        session_name('buligai_session');
        session_cache_limiter('');
        session_save_path(dirname(__DIR__, 3) . '/storage/tmp');
        session_set_cookie_params($this->cookieOptions() + ['lifetime' => 0]);
        session_start();
    }

    public function commit(): void
    {
        if ($this->codec !== null) {
            $value = $_SESSION === [] ? '' : $this->codec->encode($_SESSION);
            $expires = $value === '' ? time() - 3600 : time() + CookieSession::LIFETIME;
            setcookie($this->cookieName, $value, $this->cookieOptions() + ['expires' => $expires]);
            return;
        }
        session_write_close();
    }

    public static function rotate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    private function cookieOptions(): array
    {
        return ['path' => '/', 'httponly' => true, 'samesite' => 'Lax', 'secure' => $this->production];
    }
}
