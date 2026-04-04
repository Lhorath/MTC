<?php
use App\Includes\Csrf;
/** @var array<string, string> $errors */
?>
<h2><?= e($title) ?>.</h2>

<form method="post" action="<?= e(url('account/register')) ?>" class="form-horizontal" role="form">
    <?= Csrf::field() ?>
    <h4>Create a new account.</h4>
    <hr>
    <?php if (!empty($errors['__form'])) : ?>
        <p class="text-danger"><?= e($errors['__form']) ?></p>
    <?php endif; ?>
    <div class="form-group">
        <label class="col-md-2 control-label" for="Email">Email</label>
        <div class="col-md-10">
            <input type="email" name="Email" id="Email" class="form-control" value="<?= e($email ?? '') ?>" required>
            <?php if (!empty($errors['Email'])) : ?><span class="text-danger"><?= e($errors['Email']) ?></span><?php endif; ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2 control-label" for="Password">Password</label>
        <div class="col-md-10">
            <input type="password" name="Password" id="Password" class="form-control" required minlength="6">
            <?php if (!empty($errors['Password'])) : ?><span class="text-danger"><?= e($errors['Password']) ?></span><?php endif; ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2 control-label" for="ConfirmPassword">Confirm password</label>
        <div class="col-md-10">
            <input type="password" name="ConfirmPassword" id="ConfirmPassword" class="form-control" required>
            <?php if (!empty($errors['ConfirmPassword'])) : ?><span class="text-danger"><?= e($errors['ConfirmPassword']) ?></span><?php endif; ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-2 col-md-10">
            <button type="submit" class="btn btn-default">Register</button>
        </div>
    </div>
</form>
