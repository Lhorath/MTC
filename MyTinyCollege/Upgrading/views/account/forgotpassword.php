<?php
use App\Includes\Csrf;
/** @var array<string, string> $errors */
?>
<h2>Forgot your password?</h2>

<form method="post" action="<?= e(url('account/forgotpassword')) ?>" class="form-horizontal">
    <?= Csrf::field() ?>
    <h4>Enter your email.</h4>
    <hr>
    <div class="form-group">
        <label class="col-md-2 control-label" for="Email">Email</label>
        <div class="col-md-10">
            <input type="email" name="Email" id="Email" class="form-control" value="<?= e($email ?? '') ?>" required>
            <?php if (!empty($errors['Email'])) : ?><span class="text-danger"><?= e($errors['Email']) ?></span><?php endif; ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-2 col-md-10">
            <button type="submit" class="btn btn-default">Submit</button>
        </div>
    </div>
</form>
