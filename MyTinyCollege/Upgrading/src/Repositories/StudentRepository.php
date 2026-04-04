<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Includes\Database;
use PDO;

final class StudentRepository
{
    /** @return list<array<string, mixed>> */
    public function searchSortPage(
        ?string $sortOrder,
        ?string $searchString,
        int $page,
        int $pageSize
    ): array {
        $pdo = Database::pdo();
        $allowed = [
            '' => 's.LastName ASC',
            'lname_desc' => 's.LastName DESC',
            'fname' => 's.FirstName ASC',
            'fname_desc' => 's.FirstName DESC',
            'date' => 's.EnrollmentDate ASC',
            'date_desc' => 's.EnrollmentDate DESC',
            'email' => 's.Email ASC',
            'email_desc' => 's.Email DESC',
        ];
        $key = $sortOrder ?? '';
        $order = $allowed[$key] ?? $allowed[''];

        $where = '';
        $params = [];
        if ($searchString !== null && $searchString !== '') {
            $where = ' WHERE (s.LastName LIKE :q OR s.FirstName LIKE :q) ';
            $params[':q'] = '%' . $searchString . '%';
        }

        $countSql = "SELECT COUNT(*) FROM students s $where";
        $st = $pdo->prepare($countSql);
        foreach ($params as $k => $v) {
            $st->bindValue($k, $v);
        }
        $st->execute();
        $total = (int) $st->fetchColumn();

        $offset = max(0, ($page - 1) * $pageSize);
        $sql = "SELECT s.ID, s.LastName, s.FirstName, s.Email, s.EnrollmentDate FROM students s $where ORDER BY $order LIMIT :lim OFFSET :off";
        $st = $pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $st->bindValue($k, $v);
        }
        $st->bindValue(':lim', $pageSize, PDO::PARAM_INT);
        $st->bindValue(':off', $offset, PDO::PARAM_INT);
        $st->execute();
        /** @var list<array<string, mixed>> $rows */
        $rows = $st->fetchAll();

        return [$rows, $total];
    }

    /** @return array<string, mixed>|null */
    public function find(int $id): ?array
    {
        $st = Database::pdo()->prepare('SELECT ID, LastName, FirstName, Email, EnrollmentDate FROM students WHERE ID = :id LIMIT 1');
        $st->execute([':id' => $id]);
        $row = $st->fetch();
        return $row === false ? null : $row;
    }

    public function create(string $last, string $first, ?string $email, string $enrollmentDate): int
    {
        $st = Database::pdo()->prepare(
            'INSERT INTO students (LastName, FirstName, Email, EnrollmentDate) VALUES (:ln, :fn, :em, :ed)'
        );
        $st->execute([
            ':ln' => $last,
            ':fn' => $first,
            ':em' => $email === '' ? null : $email,
            ':ed' => $enrollmentDate,
        ]);
        return (int) Database::pdo()->lastInsertId();
    }

    public function update(int $id, string $last, string $first, ?string $email, string $enrollmentDate): void
    {
        $st = Database::pdo()->prepare(
            'UPDATE students SET LastName = :ln, FirstName = :fn, Email = :em, EnrollmentDate = :ed WHERE ID = :id'
        );
        $st->execute([
            ':ln' => $last,
            ':fn' => $first,
            ':em' => $email === '' ? null : $email,
            ':ed' => $enrollmentDate,
            ':id' => $id,
        ]);
    }

    public function delete(int $id): void
    {
        $st = Database::pdo()->prepare('DELETE FROM students WHERE ID = :id');
        $st->execute([':id' => $id]);
    }

    /** @return list<array{EnrollmentDate: string, StudentCount: int}> */
    public function statsByEnrollmentDate(): array
    {
        $sql = 'SELECT EnrollmentDate, COUNT(*) AS StudentCount FROM students GROUP BY EnrollmentDate ORDER BY EnrollmentDate';
        $st = Database::pdo()->query($sql);
        /** @var list<array{EnrollmentDate: string, StudentCount: int}> */
        return $st->fetchAll();
    }
}
