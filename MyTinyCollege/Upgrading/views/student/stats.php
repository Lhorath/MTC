<?php /** @var list<array<string, mixed>> $stats */ ?>
<h2>Student Body Statistics</h2>
<table class="table table-condensed table-striped">
    <thead>
        <tr>
            <th>Enrollment Date</th>
            <th>Students</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($stats as $item) : ?>
            <tr>
                <td><?= e((string) $item['EnrollmentDate']) ?></td>
                <td><?= (int) $item['StudentCount'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
