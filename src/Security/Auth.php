<?php

declare(strict_types=1);

namespace App\Security;

use App\Model\User;

final class Auth
{
    private const SESSION_KEY = '_authenticated_user';

    public static function check(): bool
    {
        return isset($_SESSION[self::SESSION_KEY]);
    }

    public static function login(string $email, string $password): bool
    {
        $user = User::query()->where('email', trim($email))->first();

        if ($user === null || !hash_equals((string) $user->password, $password)) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION[self::SESSION_KEY] = $user->email;

        return true;
    }

    public static function userEmail(): ?string
    {
        $email = $_SESSION[self::SESSION_KEY] ?? null;

        return is_string($email) ? $email : null;
    }

    public static function register(string $name, string $email, string $password): User
    {
        return User::query()->create([
            'name' => trim($name),
            'email' => trim($email),
            'password' => $password,
        ]);
    }

    public static function logout(): void
    {
        unset($_SESSION[self::SESSION_KEY]);
        session_regenerate_id(true);
    }
}
