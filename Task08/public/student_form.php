<?php
$id = $_GET['id'] ?? 0;
$student = null;
$title = 'Добавление студента';

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$id]);
    $student = $stmt->fetch();
    $title = 'Редактирование студента';
}

$groups = $pdo->query("SELECT * FROM groups ORDER BY group_number")->fetchAll();
?>

<h2><?= $title ?></h2>

<form method="post" class="form-container">
    <input type="hidden" name="id" value="<?= $id ?>">
    
    <div class="form-group">
        <label for="last_name">Фамилия:</label>
        <input type="text" id="last_name" name="last_name" class="form-control" 
               value="<?= htmlspecialchars($student['last_name'] ?? '') ?>" required>
    </div>
    
    <div class="form-group">
        <label for="first_name">Имя:</label>
        <input type="text" id="first_name" name="first_name" class="form-control" 
               value="<?= htmlspecialchars($student['first_name'] ?? '') ?>" required>
    </div>
    
    <div class="form-group">
        <label for="group_id">Группа:</label>
        <select id="group_id" name="group_id" class="form-control" required>
            <option value="">Выберите группу</option>
            <?php foreach($groups as $group): ?>
                <option value="<?= $group['id'] ?>" <?= ($student['group_id'] ?? '') == $group['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($group['group_number']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="form-group">
        <label>Пол:</label>
        <div class="radio-group">
            <label>
                <input type="radio" name="gender" value="M" <?= ($student['gender'] ?? '') == 'M' ? 'checked' : '' ?> required>
                Мужской
            </label>
            <label>
                <input type="radio" name="gender" value="F" <?= ($student['gender'] ?? '') == 'F' ? 'checked' : '' ?>>
                Женский
            </label>
        </div>
    </div>
    
    <div class="form-actions">
        <button type="submit" name="save_student" class="btn btn-success">Сохранить</button>
        <a href="?action=students" class="btn btn-secondary">Отмена</a>
    </div>
</form>