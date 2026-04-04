<h2><?= e($title ?? 'Error') ?></h2>
<p class="text-danger"><?= e($message ?? '') ?></p>
<p><a href="<?= e(url('home/index')) ?>">Home</a></p>
