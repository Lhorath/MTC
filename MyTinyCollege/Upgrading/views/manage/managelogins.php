<h2><?= e($title) ?></h2>

<?php if (!empty($statusMessage)) : ?>
    <p class="text-success"><?= e($statusMessage) ?></p>
<?php endif; ?>

<p>There are no external authentication services configured. See <a href="https://go.microsoft.com/fwlink/?LinkId=403804">this article</a> for details.</p>
<p><a href="<?= e(url('manage/index')) ?>">Back to manage</a></p>
