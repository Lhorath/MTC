<?php
/** @var list<array<string, mixed>> $students */
/** @var \App\Services\Pager $pager */
/** @var array<string, mixed> $viewBag */
$vb = $viewBag;
?>
<h2>Index</h2>

<p><a href="<?= e(url('student/create')) ?>">Create New</a></p>

<form method="get" action="<?= e(base_url('index.php')) ?>" class="form-inline">
    <input type="hidden" name="r" value="student/index">
    <input type="hidden" name="sortOrder" value="<?= e((string) ($vb['CurrentSort'] ?? '')) ?>">
    <p>
        Find By Name:
        <input type="text" name="searchString" value="<?= e((string) ($vb['CurrentFilter'] ?? '')) ?>" class="form-control">
        <button type="submit" class="btn btn-sm btn-default">Search</button>
    </p>
</form>

<table class="table">
    <tr>
        <th><a href="<?= e(url('student/index', ['sortOrder' => $vb['LNameSortParm'], 'currentFilter' => $vb['CurrentFilter'] ?? ''])) ?>">Last Name</a></th>
        <th><a href="<?= e(url('student/index', ['sortOrder' => $vb['FNameSortParm'], 'currentFilter' => $vb['CurrentFilter'] ?? ''])) ?>">First Name</a></th>
        <th><a href="<?= e(url('student/index', ['sortOrder' => $vb['EmailSortParm'], 'currentFilter' => $vb['CurrentFilter'] ?? ''])) ?>">Email</a></th>
        <th><a href="<?= e(url('student/index', ['sortOrder' => $vb['DateSortParm'], 'currentFilter' => $vb['CurrentFilter'] ?? ''])) ?>">Enrollment Date</a></th>
        <th></th>
    </tr>
    <?php foreach ($students as $item) : ?>
        <tr>
            <td><?= e((string) $item['LastName']) ?></td>
            <td><?= e((string) $item['FirstName']) ?></td>
            <td><?= e((string) ($item['Email'] ?? '')) ?></td>
            <td><?= e((string) $item['EnrollmentDate']) ?></td>
            <td>
                <a href="<?= e(url('student/edit/' . (int) $item['ID'])) ?>">Edit</a> |
                <a href="<?= e(url('student/details/' . (int) $item['ID'])) ?>">Details</a> |
                <a href="<?= e(url('student/delete/' . (int) $item['ID'])) ?>">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php
$pc = $pager->pageCount();
$pn = $pager->pageNumber;
$displayPn = $pc < $pn ? 0 : $pn;
?>
<br>
<p>Page <?= (int) $displayPn ?> of <?= (int) $pc ?></p>
<ul class="pagination">
    <?php for ($i = 1; $i <= $pc; $i++) : ?>
        <li class="<?= $i === $pn ? 'active' : '' ?>">
            <a href="<?= e(url('student/index', [
                'page' => (string) $i,
                'sortOrder' => (string) ($vb['CurrentSort'] ?? ''),
                'currentFilter' => (string) ($vb['CurrentFilter'] ?? ''),
            ])) ?>"><?= $i ?></a>
        </li>
    <?php endfor; ?>
</ul>
