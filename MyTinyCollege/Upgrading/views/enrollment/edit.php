<?php
use App\Includes\Csrf;
/** @var array<string, mixed> $model */
/** @var array<string, string> $errors */
?>
<h2>Edit Grade</h2>
<p>Student: <?= e((string) $model['FirstName'] . ' ' . (string) $model['LastName']) ?> — <?= e((string) $model['CourseTitle']) ?></p>

<form method="post" action="<?= e(url('enrollment/edit/' . (int) $model['EnrollmentID'])) ?>" class="form-horizontal">
    <?= Csrf::field() ?>
    <div class="form-group">
        <label class="control-label col-md-2" for="Grade">Grade</label>
        <div class="col-md-10">
            <select name="Grade" id="Grade" class="form-control">
                <option value="">No grade</option>
                <?php foreach (['A', 'B', 'C', 'D', 'F'] as $g) : ?>
                    <option value="<?= $g ?>" <?= (($model['Grade'] ?? '') === $g) ? 'selected' : '' ?>><?= $g ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['Grade'])) : ?><span class="text-danger"><?= e($errors['Grade']) ?></span><?php endif; ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-2 col-md-10">
            <button type="submit" class="btn btn-default">Save</button>
            <a href="<?= e(url('student/details/' . (int) $model['StudentID'])) ?>" class="btn btn-link">Cancel</a>
        </div>
    </div>
</form>
