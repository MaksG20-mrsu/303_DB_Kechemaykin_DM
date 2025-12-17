<?php
$id = $_GET['id'] ?? 0;
$student_id = $_GET['student_id'] ?? 0;

if (!$id || !$student_id) {
    header('Location: ?action=students');
    exit;
}

$stmt = $pdo->prepare("
    SELECT er.*, d.name as discipline_name, s.last_name, s.first_name 
    FROM exam_results er 
    JOIN disciplines d ON er.discipline_id = d.id 
    JOIN students s ON er.student_id = s.id 
    WHERE er.id = ?
");
$stmt->execute([$id]);
$exam = $stmt->fetch();

if (!$exam) {
    header('Location: ?action=students');
    exit;
}
?>

<h2>Удаление результата экзамена</h2>

<div class="alert alert-danger">
    <p>Вы действительно хотите удалить результат экзамена?</p>
    <p>
        Студент: <strong><?= htmlspecialchars($exam['last_name']) ?> <?= htmlspecialchars($exam['first_name']) ?></strong><br>
        Дисциплина: <strong><?= htmlspecialchars($exam['discipline_name']) ?></strong><br>
        Дата: <strong><?= date('d.m.Y', strtotime($exam['exam_date'])) ?></strong><br>
        Оценка: <strong><?= $exam['grade'] ?></strong>
    </p>
</div>

<form method="post">
    <input type="hidden" name="id" value="<?= $id ?>">
    <input type="hidden" name="student_id" value="<?= $student_id ?>">
    <div class="form-actions">
        <button type="submit" name="delete_exam" class="btn btn-danger">Удалить</button>
        <a href="?action=exams&student_id=<?= $student_id ?>" class="btn btn-secondary">Отмена</a>
    </div>
</form>