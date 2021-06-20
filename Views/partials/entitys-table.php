<?php use EntityList\Helpers\UrlManager; ?>
<table>
    <thead>
    <tr>
        <th>Имя</th>
        <th>Фамилия</th>
        <th>Номер группы</th>
        <th>Баллы ЕГЭ</th>
    </tr>
    </thead>
    <tbody>
	<?php foreach ($entitys as $entity): ?>
        <tr>
            <td><?php echo htmlspecialchars($entity["name"], ENT_QUOTES) ?></td>
            <td><?php echo htmlspecialchars($entity["surname"], ENT_QUOTES) ?></td>
            <td><?php echo htmlspecialchars($entity["group_number"], ENT_QUOTES) ?></td>
            <td><?php echo htmlspecialchars($entity["exam_score"], ENT_QUOTES) ?></td>
        </tr>
	<?php endforeach; ?>
    </tbody>
</table>
<div class="pagination">
	<?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?php echo "{$i}" . "&" . htmlspecialchars(UrlManager::getPaginationLink(
				$order,
				$direction,
				$search
			), ENT_QUOTES); ?>"><?php echo $i; ?></a>
	<?php endfor; ?>
</div>
