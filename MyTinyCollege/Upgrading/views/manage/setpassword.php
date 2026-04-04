<?php
use App\Includes\Csrf;
/** @var array<string, string> $errors */
?>
<h2>Set password</h2>

<form method="post" action="<?= e(url('manage/setpassword')) ?>" class="form-horizontal">
    <?= Csrf::field() ?>
    <hr>
    <div class="form-group">
        <label class="col-md-2 control-label" for="NewPassword">New password</label>
        <div class="col-md-10">
            <input type="password" name="NewPassword" id="NewPassword" class="form-control" required minlength="6">
            <?php if (!empty($errors['NewPassword'])) : ?><span class="text-danger"><?= e($errors['NewPassword']) ?></span><?php endif; ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2 control-label" for="ConfirmPassword">Confirm new password</label>
        <div class="col-md-10">
            <input type="password" name="ConfirmPassword" id="ConfirmPassword" class="form-control" required>
            <?php if (!empty($errors['ConfirmPassword'])) : ?><span class="text-danger"><?= e($errors['ConfirmPassword']) ?></span><?php endif; ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-2 col-md-10">
            <button type="submit" class="btn btn-default">Set password</button>
        </div>
    </div>
</form>
