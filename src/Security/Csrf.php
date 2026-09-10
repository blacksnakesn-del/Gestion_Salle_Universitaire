<?php

declare(strict_types=1);

namespace App\Security;

use RuntimeException;

final class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }

        return (string) $_SESSION['_csrf_token'];
    }

    public static function verify(?string $token): void
    {
        $expected = $_SESSION['_csrf_token'] ?? null;

        if (!is_string($expected) || !is_string($token) || !hash_equals($expected, $token)) {
            throw new RuntimeException('Le formulaire a expiré. Veuillez réessayer.');
        }
    }
}