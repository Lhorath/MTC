<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Includes\Database;

final class EnrollmentRepository
{
    /** @return list<array<string, mixed>> */
    public function forStudent(int $studentId): array
    {
        $sql = <<<'SQL'
            SELECT e.EnrollmentID, e.CourseID, e.StudentID, e.Grade,
                   c.Title AS CourseTitle, c.Credits
            FROM enrollments e
            INNER JOIN courses c ON c.CourseID = e.CourseID
            WHERE e.StudentID = :sid
            ORDER BY c.Title
            SQL;
        $st = Database::pdo()->prepare($sql);
        $st->execute([':sid' => $studentId]);
        /** @var list<array<string, mixed>> */
        return $st->fetchAll();
    }

    /** @return array<string, mixed>|null */
    public function find(int $enrollmentId): ?array
    {
        $sql = <<<'SQL'
            SELECT e.EnrollmentID, e.CourseID, e.StudentID, e.Grade,
                   c.Title AS CourseTitle, s.FirstName, s.LastName
            FROM enrollments e
            INNER JOIN courses c ON c.CourseID = e.CourseID
            INNER JOIN students s ON s.ID = e.StudentID
            WHERE e.EnrollmentID = :eid
            SQL;
        $st = Database::pdo()->prepare($sql);
        $st->execute([':eid' => $enrollmentId]);
        $row = $st->fetch();
        return $row === false ? null : $row;
    }

    public function updateGrade(int $enrollmentId, ?string $grade): void
    {
        $st = Database::pdo()->prepare('UPDATE enrollments SET Grade = :g WHERE EnrollmentID = :id');
        $st->execute([':g' => $grade, ':id' => $enrollmentId]);
    }
}
