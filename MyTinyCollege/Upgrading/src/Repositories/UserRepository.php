<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Includes\Database;

final class UserRepository
{
    /** @return array{id: int, email: string, password_hash: string}|null */
    public function findByEmail(string $email): ?array
    {
        $st = Database::pdo()->prepare('SELECT id, email, password_hash FROM users WHERE email = :e LIMIT 1');
        $st->execute([':e' => $email]);
        $row = $st->fetch();
        return $row === false ? null : $row;
    }

    /** @return array{id: int, email: string, password_hash: string}|null */
    public function findById(int $id): ?array
    {
        $st = Database::pdo()->prepare('SELECT id, email, password_hash FROM users WHERE id = :id LIMIT 1');
        $st->execute([':id' => $id]);
        $row = $st->fetch();
        return $row === false ? null : $row;
    }

    public function create(string $email, string $passwordHash): int
    {
        $st = Database::pdo()->prepare('INSERT INTO users (email, password_hash) VALUES (:e, :p)');
        $st->execute([':e' => $email, ':p' => $passwordHash]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function updatePassword(int $userId, string $passwordHash): void
    {
        $st = Database::pdo()->prepare('UPDATE users SET password_hash = :p WHERE id = :id');
        $st->execute([':p' => $passwordHash, ':id' => $userId]);
    }

    public function storeResetToken(string $email, string $token, string $expiresAt): void
    {
        $pdo = Database::pdo();
        $pdo->prepare('DELETE FROM password_resets WHERE email = :e')->execute([':e' => $email]);
        $st = $pdo->prepare('INSERT INTO password_resets (email, token, expires_at) VALUES (:e, :t, :x)');
        $st->execute([':e' => $email, ':t' => $token, ':x' => $expiresAt]);
    }

    /** @return array{email: string}|null */
    public function findValidResetToken(string $token): ?array
    {
        $st = Database::pdo()->prepare(
            'SELECT email FROM password_resets WHERE token = :t AND expires_at > NOW() LIMIT 1'
        );
        $st->execute([':t' => $token]);
        $row = $st->fetch();
        return $row === false ? null : $row;
    }

    public function deleteResetToken(string $token): void
    {
        Database::pdo()->prepare('DELETE FROM password_resets WHERE token = :t')->execute([':t' => $token]);
    }
}
