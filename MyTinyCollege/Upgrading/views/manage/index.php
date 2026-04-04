<h2><?= e($title) ?>.</h2>

<?php if (!empty($statusMessage)) : ?>
    <p class="text-success"><?= e($statusMessage) ?></p>
<?php endif; ?>

<div>
    <h4>Change your account settings</h4>
    <hr>
    <dl class="dl-horizontal">
        <dt>Password:</dt>
        <dd>
            [
            <?php if (!empty($hasPassword)) : ?>
                <a href="<?= e(url('manage/changepassword')) ?>">Change your password</a>
            <?php else : ?>
                <a href="<?= e(url('manage/setpassword')) ?>">Create</a>
            <?php endif; ?>
            ]
        </dd>
        <dt>External Logins:</dt>
        <dd>
            0 [
            <a href="<?= e(url('manage/managelogins')) ?>">Manage</a> ]
        </dd>
        <dt>Two-Factor Authentication:</dt>
        <dd>
            <p>There are no two-factor authentication providers configured. See <a href="https://go.microsoft.com/fwlink/?LinkId=403804">this article</a> for details.</p>
        </dd>
    </dl>
</div>
