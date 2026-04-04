<h2><?= e($title) ?></h2>
<?php if (!empty($ok)) : ?>
    <p>Thank you for confirming your email.</p>
<?php else : ?>
    <p>Email confirmation is not configured in this PHP port.</p>
<?php endif; ?>
