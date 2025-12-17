<?php
$id = $_GET['id'] ?? 0;
if (!$id) {
    header('Location: ?action=students');
    exit;
}

$stmt = $pdo->prepare("SELECT s.*, g.group_number FROM students s JOIN groups g ON s.group_id = g.id WHERE s.id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    header('Location: ?action=students');
    exit;
}
?>

<h2>Удаление студента</h2>

<div class="alert alert-danger">
    <p>Вы действительно хотите удалить студента <strong><?= htmlspecialchars($student['last_name']) ?> <?= htmlspecialchars($student['first_name']) ?></strong> из группы <strong><?= htmlspecialchars($student['group_number']) ?></strong>?</p>
    <p>Все результаты экзаменов этого студента также будут удалены!</p>
</div>

<form method="post">
    <input type="hidden" name="id" value="<?= $id ?>">
    <div class="form-actions">
        <button type="submit" name="delete_student" class="btn btn-danger">Удалить</button>
        <a href="?action=students" class="btn btn-secondary">Отмена</a>
    </div>
</form>