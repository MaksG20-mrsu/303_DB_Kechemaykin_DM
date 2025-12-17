<?php
if (!$student_id) {
    header('Location: ?action=students');
    exit;
}

// Получение информации о студенте
$stmt = $pdo->prepare("SELECT s.*, g.group_number FROM students s JOIN groups g ON s.group_id = g.id WHERE s.id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    header('Location: ?action=students');
    exit;
}

// Получение экзаменов студента
$stmt = $pdo->prepare("
    SELECT er.*, d.name as discipline_name, d.course 
    FROM exam_results er 
    JOIN disciplines d ON er.discipline_id = d.id 
    WHERE er.student_id = ? 
    ORDER BY er.exam_date DESC
");
$stmt->execute([$student_id]);
$exams = $stmt->fetchAll();
?>

<h2>📊 Результаты экзаменов: <?= htmlspecialchars($student['last_name']) ?> <?= htmlspecialchars($student['first_name']) ?></h2>
<p>Группа: <?= htmlspecialchars($student['group_number']) ?></p>

<div class="table-container">
    <table class="table">
        <thead>
            <tr>
                <th>Дисциплина</th>
                <th>Курс</th>
                <th>Дата экзамена</th>
                <th>Оценка</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($exams)): ?>
                <tr>
                    <td colspan="5" style="text-align: center;">Нет данных об экзаменах</td>
                </tr>
            <?php else: ?>
                <?php foreach($exams as $exam): ?>
                    <tr>
                        <td><?= htmlspecialchars($exam['discipline_name']) ?></td>
                        <td><?= $exam['course'] ?></td>
                        <td><?= date('d.m.Y', strtotime($exam['exam_date'])) ?></td>
                        <td>
                            <span class="grade-badge grade-<?= $exam['grade'] ?>">
                                <?= $exam['grade'] ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="?action=edit_exam&id=<?= $exam['id'] ?>&student_id=<?= $student_id ?>" class="btn action-btn">✏️ Редактировать</a>
                                <a href="?action=delete_exam&id=<?= $exam['id'] ?>&student_id=<?= $student_id ?>" class="btn action-btn btn-danger">🗑️ Удалить</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>