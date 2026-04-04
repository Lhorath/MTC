<?php
use App\Includes\Csrf;
/** @var array<string, string> $errors */
/** @var array<string, mixed> $model */
?>
<h2>Edit</h2>

<form method="post" action="<?= e(url('student/edit/' . (int) $model['ID'])) ?>" class="form-horizontal">
    <?= Csrf::field() ?>
    <h4>Student</h4>
    <hr>
    <input type="hidden" name="ID" value="<?= (int) $model['ID'] ?>">
    <?php if (!empty($errors['__form'])) : ?>
        <p class="text-danger"><?= e($errors['__form']) ?></p>
    <?php endif; ?>
    <div class="form-group">
        <label class="control-label col-md-2" for="LastName">Last Name</label>
        <div class="col-md-10">
            <input type="text" name="LastName" id="LastName" class="form-control" value="<?= e((string) $model['LastName']) ?>" maxlength="65" required>
            <?php if (!empty($errors['LastName'])) : ?><span class="text-danger"><?= e($errors['LastName']) ?></span><?php endif; ?>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-2" for="FirstName">First Name</label>
        <div class="col-md-10">
            <input type="text" name="FirstName" id="FirstName" class="form-control" value="<?= e((string) $model['FirstName']) ?>" maxlength="50" required>
            <?php if (!empty($errors['FirstName'])) : ?><span class="text-danger"><?= e($errors['FirstName']) ?></span><?php endif; ?>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-2" for="Email">Email</label>
        <div class="col-md-10">
            <input type="email" name="Email" id="Email" class="form-control" value="<?= e((string) ($model['Email'] ?? '')) ?>">
            <?php if (!empty($errors['Email'])) : ?><span class="text-danger"><?= e($errors['Email']) ?></span><?php endif; ?>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-2" for="EnrollmentDate">Enrollment Date</label>
        <div class="col-md-10">
            <input type="date" name="EnrollmentDate" id="EnrollmentDate" class="form-control" value="<?= e((string) $model['EnrollmentDate']) ?>" required>
            <?php if (!empty($errors['EnrollmentDate'])) : ?><span class="text-danger"><?= e($errors['EnrollmentDate']) ?></span><?php endif; ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-2 col-md-10">
            <button type="submit" class="btn btn-default">Save</button>
        </div>
    </div>
</form>

<p><a href="<?= e(url('student/index')) ?>">Back to List</a></p>
