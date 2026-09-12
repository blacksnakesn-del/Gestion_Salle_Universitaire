<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\User;
use App\Security\Auth;
use App\Security\Csrf;
use Illuminate\Database\QueryException;

final class AuthController
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            header('Location: /salles');
            exit;
        }

        $error = null;
        $old = ['email' => ''];
        $csrfToken = Csrf::token();

        renderView('auth/login', compact('error', 'old', 'csrfToken'));
    }

    public function login(): void
    {
        $post = $_POST ?? [];
        $email = trim((string) ($post['email'] ?? ''));
        $password = (string) ($post['password'] ?? '');
        $old = ['email' => $email];
        $error = null;

        try {
            Csrf::verify($post['_csrf_token'] ?? null);
        } catch (\RuntimeException $exception) {
            $error = $exception->getMessage();
        }

        if ($error === null && !Auth::login($email, $password)) {
            $error = 'Adresse email ou mot de passe incorrect.';
        }

        if ($error !== null) {
            $csrfToken = Csrf::token();
            renderView('auth/login', compact('error', 'old', 'csrfToken'));
            return;
        }

        header('Location: /salles');
        exit;
    }

    public function showRegister(): void
    {
        if (Auth::check()) {
            header('Location: /salles');
            exit;
        }

        $error = null;
        $old = ['name' => '', 'email' => ''];
        $csrfToken = Csrf::token();

        renderView('auth/register', compact('error', 'old', 'csrfToken'));
    }

    public function register(): void
    {
        $post = $_POST ?? [];
        $old = [
            'name' => trim((string) ($post['name'] ?? '')),
            'email' => trim((string) ($post['email'] ?? '')),
        ];
        $password = (string) ($post['password'] ?? '');
        $confirmation = (string) ($post['password_confirmation'] ?? '');
        $error = null;

        try {
            Csrf::verify($post['_csrf_token'] ?? null);
        } catch (\RuntimeException $exception) {
            $error = $exception->getMessage();
        }

        if ($error === null && $old['name'] === '') {
            $error = 'Le nom est obligatoire.';
        } elseif ($error === null && !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
            $error = 'L’adresse email est invalide.';
        } elseif ($error === null && strlen($password) < 6) {
            $error = 'Le mot de passe doit contenir au moins 6 caractères.';
        } elseif ($error === null && $password !== $confirmation) {
            $error = 'Les mots de passe ne correspondent pas.';
        }

        if ($error === null && User::query()->where('email', $old['email'])->exists()) {
            $error = 'Cette adresse email est déjà utilisée.';
        }

        if ($error === null) {
            try {
                Auth::register($old['name'], $old['email'], $password);
                Auth::login($old['email'], $password);

                header('Location: /salles');
                exit;
            } catch (QueryException) {
                $error = 'Impossible de créer ce compte. Vérifiez les informations saisies.';
            }
        }

        $csrfToken = Csrf::token();
        renderView('auth/register', compact('error', 'old', 'csrfToken'));
    }

    public function logout(): void
    {
        Csrf::verify($_POST['_csrf_token'] ?? null);
        Auth::logout();

        header('Location: /login');
        exit;
    }
}
