<?php
/** @var array<string, mixed> $student */
/** @var list<array<string, mixed>> $enrollments */
?>
<h2>Details</h2>
<div>
    <h4>Student</h4>
    <hr>
    <dl class="dl-horizontal">
        <dt>Last Name</dt>
        <dd><?= e((string) $student['LastName']) ?></dd>
        <dt>First Name</dt>
        <dd><?= e((string) $student['FirstName']) ?></dd>
        <dt>Email</dt>
        <dd><?= e((string) ($student['Email'] ?? '')) ?></dd>
        <dt>Enrollment Date</dt>
        <dd><?= e((string) $student['EnrollmentDate']) ?></dd>
        <dt>Enrollments</dt>
        <dd>
            <table class="table">
                <tr>
                    <th>Course Title</th>
                    <th>Grade</th>
                    <th>&nbsp;</th>
                </tr>
                <?php foreach ($enrollments as $item) : ?>
                    <?php
                    $gradeVal = $item['Grade'] ?? null;
                    $gradeEmpty = $gradeVal === null || $gradeVal === '';
                    ?>
                    <tr>
                        <td><?= e((string) $item['CourseTitle']) ?></td>
                        <td><?= e($gradeEmpty ? 'No grade' : (string) $gradeVal) ?></td>
                        <td>
                            <?php if ($gradeEmpty) : ?>
                                <a href="<?= e(url('enrollment/edit/' . (int) $item['EnrollmentID'])) ?>" title="Add Grade">Add Grade</a>
                                <span class="glyphicon glyphicon-edit"></span>
                            <?php else : ?>
                                <a href="<?= e(url('enrollment/edit/' . (int) $item['EnrollmentID'])) ?>" title="Edit Grade">Edit Grade</a>
                                <span class="glyphicon glyphicon-pencil"></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </dd>
    </dl>
</div>
<p>
    <a href="<?= e(url('student/edit/' . (int) $student['ID'])) ?>">Edit</a> |
    <a href="<?= e(url('student/index')) ?>">Back to List</a>
</p>
