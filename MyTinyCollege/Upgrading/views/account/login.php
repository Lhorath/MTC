<?php
use App\Includes\Csrf;
/** @var array<string, string> $errors */
?>
<h2><?= e($title) ?>.</h2>
<div class="row">
    <div class="col-md-8">
        <section id="loginForm">
            <form method="post" action="<?= e(url('account/login')) ?>" class="form-horizontal" role="form">
                <?= Csrf::field() ?>
                <input type="hidden" name="ReturnUrl" value="<?= e($returnUrl ?? '') ?>">
                <h4>Use a local account to log in.</h4>
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
                        <input type="password" name="Password" id="Password" class="form-control" required>
                        <?php if (!empty($errors['Password'])) : ?><span class="text-danger"><?= e($errors['Password']) ?></span><?php endif; ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-2 col-md-10">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="RememberMe" value="1" <?= !empty($remember) ? 'checked' : '' ?>> Remember me?
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-2 col-md-10">
                        <button type="submit" class="btn btn-default">Log in</button>
                    </div>
                </div>
                <p><a href="<?= e(url('account/register')) ?>">Register as a new user</a></p>
            </form>
        </section>
    </div>
    <div class="col-md-4">
        <section id="socialLoginForm">
            <h4>Use another service to log in.</h4>
            <hr>
            <div>
                <p>There are no external authentication services configured. See <a href="https://go.microsoft.com/fwlink/?LinkId=403804">this article</a> for details on setting up logging in via external services.</p>
            </div>
        </section>
    </div>
</div>
