<?php
$id = $_GET['id'] ?? 0;
$exam = null;
$title = 'Добавление экзамена';

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM exam_results WHERE id = ?");
    $stmt->execute([$id]);
    $exam = $stmt->fetch();
    $title = 'Редактирование экзамена';
}

$student_id = $_GET['student_id'] ?? $exam['student_id'];
$disciplines = $pdo->query("SELECT * FROM disciplines ORDER BY name")->fetchAll();
?>

<h2><?= $title ?></h2>

<form method="post" class="form-container">
    <input type="hidden" name="id" value="<?= $id ?>">
    <input type="hidden" name="student_id" value="<?= $student_id ?>">
    
    <div class="form-group">
        <label for="discipline_id">Дисциплина:</label>
        <select id="discipline_id" name="discipline_id" class="form-control" required>
            <option value="">Выберите дисциплину</option>
            <?php foreach($disciplines as $discipline): ?>
                <option value="<?= $discipline['id'] ?>" <?= ($exam['discipline_id'] ?? '') == $discipline['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($discipline['name']) ?> (<?= $discipline['course'] ?> курс)
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="form-group">
        <label for="exam_date">Дата экзамена:</label>
        <input type="date" id="exam_date" name="exam_date" class="form-control" 
               value="<?= $exam['exam_date'] ?? date('Y-m-d') ?>" required>
    </div>
    
    <div class="form-group">
        <label for="grade">Оценка:</label>
        <select id="grade" name="grade" class="form-control" required>
            <option value="5" <?= ($exam['grade'] ?? '') == 5 ? 'selected' : '' ?>>5 (Отлично)</option>
            <option value="4" <?= ($exam['grade'] ?? '') == 4 ? 'selected' : '' ?>>4 (Хорошо)</option>
            <option value="3" <?= ($exam['grade'] ?? '') == 3 ? 'selected' : '' ?>>3 (Удовлетворительно)</option>
            <option value="2" <?= ($exam['grade'] ?? '') == 2 ? 'selected' : '' ?>>2 (Неудовлетворительно)</option>
        </select>
    </div>
    
    <div class="form-actions">
        <button type="submit" name="save_exam" class="btn btn-success">Сохранить</button>
        <a href="?action=exams&student_id=<?= $student_id ?>" class="btn btn-secondary">Отмена</a>
    </div>
</form>