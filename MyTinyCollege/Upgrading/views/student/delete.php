<?php
use App\Includes\Csrf;
/** @var array<string, mixed> $student */
/** @var bool $saveError */
?>
<h2>Delete</h2>
<?php if (!empty($saveError)) : ?>
    <p class="has-error-msg">Delete failed please try again.</p>
<?php endif; ?>
<h3>Are you sure you want to delete this?</h3>
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
    </dl>
    <form method="post" action="<?= e(url('student/delete/' . (int) $student['ID'])) ?>">
        <?= Csrf::field() ?>
        <div class="form-actions no-color">
            <button type="submit" class="btn btn-default">Delete</button> |
            <a href="<?= e(url('student/index')) ?>">Back to List</a>
        </div>
    </form>
</div>
