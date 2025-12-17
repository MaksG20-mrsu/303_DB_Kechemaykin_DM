<?php
// Фильтр по группе
$where = '';
$params = [];
if ($group_filter) {
    $where = "WHERE g.group_number = ?";
    $params[] = $group_filter;
}

// Получение групп для фильтра
$groups = $pdo->query("SELECT * FROM groups ORDER BY group_number")->fetchAll();

// Получение студентов
$stmt = $pdo->prepare("
    SELECT s.*, g.group_number 
    FROM students s 
    JOIN groups g ON s.group_id = g.id 
    $where 
    ORDER BY g.group_number, s.last_name
");
$stmt->execute($params);
$students = $stmt->fetchAll();
?>

<div class="filter">
    <form method="get">
        <input type="hidden" name="action" value="students">
        <label for="group_filter">Фильтр по группе:</label>
        <select name="group_filter" id="group_filter" onchange="this.form.submit()">
            <option value="">Все группы</option>
            <?php foreach($groups as $group): ?>
                <option value="<?= $group['group_number'] ?>" <?= $group_filter == $group['group_number'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($group['group_number']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<div class="table-container">
    <table class="table">
        <thead>
            <tr>
                <th>Фамилия</th>
                <th>Имя</th>
                <th>Группа</th>
                <th>Пол</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($students)): ?>
                <tr>
                    <td colspan="5" style="text-align: center;">Нет данных</td>
                </tr>
            <?php else: ?>
                <?php foreach($students as $student): ?>
                    <tr>
                        <td><?= htmlspecialchars($student['last_name']) ?></td>
                        <td><?= htmlspecialchars($student['first_name']) ?></td>
                        <td><?= htmlspecialchars($student['group_number']) ?></td>
                        <td><?= $student['gender'] == 'M' ? 'Мужской' : 'Женский' ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="?action=edit_student&id=<?= $student['id'] ?>" class="btn action-btn">✏️ Редактировать</a>
                                <a href="?action=exams&student_id=<?= $student['id'] ?>" class="btn action-btn btn-secondary">📊 Экзамены</a>
                                <a href="?action=delete_student&id=<?= $student['id'] ?>" class="btn action-btn btn-danger">🗑️ Удалить</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>