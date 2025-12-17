<?php
require_once '../database.php';

$db = Database::getInstance();
$pdo = $db->getConnection();

// Определение действия
$action = $_GET['action'] ?? 'students';
$student_id = $_GET['student_id'] ?? null;
$exam_id = $_GET['exam_id'] ?? null;
$group_filter = $_GET['group_filter'] ?? null;

// Обработка POST запросов
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_student'])) {
        $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        header('Location: ?action=students');
        exit;
    }
    
    if (isset($_POST['delete_exam'])) {
        $stmt = $pdo->prepare("DELETE FROM exam_results WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        header('Location: ?action=exams&student_id=' . $_POST['student_id']);
        exit;
    }
    
    if (isset($_POST['save_student'])) {
        if ($_POST['id']) {
            // Обновление
            $stmt = $pdo->prepare("UPDATE students SET last_name = ?, first_name = ?, group_id = ?, gender = ? WHERE id = ?");
            $stmt->execute([$_POST['last_name'], $_POST['first_name'], $_POST['group_id'], $_POST['gender'], $_POST['id']]);
        } else {
            // Добавление
            $stmt = $pdo->prepare("INSERT INTO students (last_name, first_name, group_id, gender) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_POST['last_name'], $_POST['first_name'], $_POST['group_id'], $_POST['gender']]);
        }
        header('Location: ?action=students');
        exit;
    }
    
    if (isset($_POST['save_exam'])) {
        if ($_POST['id']) {
            // Обновление
            $stmt = $pdo->prepare("UPDATE exam_results SET student_id = ?, discipline_id = ?, exam_date = ?, grade = ? WHERE id = ?");
            $stmt->execute([$_POST['student_id'], $_POST['discipline_id'], $_POST['exam_date'], $_POST['grade'], $_POST['id']]);
        } else {
            // Добавление
            $stmt = $pdo->prepare("INSERT INTO exam_results (student_id, discipline_id, exam_date, grade) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_POST['student_id'], $_POST['discipline_id'], $_POST['exam_date'], $_POST['grade']]);
        }
        header('Location: ?action=exams&student_id=' . $_POST['student_id']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Учет студентов и экзаменов</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📚 Учет студентов и экзаменов</h1>
            <div class="actions">
                <?php if ($action === 'students'): ?>
                    <a href="?action=add_student" class="btn btn-success">➕ Добавить студента</a>
                <?php elseif ($action === 'exams' && $student_id): ?>
                    <a href="?action=add_exam&student_id=<?= $student_id ?>" class="btn btn-success">➕ Добавить экзамен</a>
                    <a href="?action=students" class="btn btn-secondary">← Назад к списку</a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="breadcrumb">
            <?php if ($action !== 'students'): ?>
                <a href="?action=students">Студенты</a>
                <?php if ($action === 'exams'): ?>
                    → Экзамены
                <?php elseif (in_array($action, ['add_student', 'edit_student'])): ?>
                    → <?= $action === 'add_student' ? 'Добавление' : 'Редактирование' ?> студента
                <?php elseif (in_array($action, ['add_exam', 'edit_exam'])): ?>
                    → Экзамены → <?= $action === 'add_exam' ? 'Добавление' : 'Редактирование' ?> экзамена
                <?php endif; ?>
            <?php else: ?>
                <span>Список студентов</span>
            <?php endif; ?>
        </div>

        <?php
        switch($action) {
            case 'add_student':
            case 'edit_student':
                include 'student_form.php';
                break;
                
            case 'delete_student':
                include 'delete_student.php';
                break;
                
            case 'exams':
                include 'exams_list.php';
                break;
                
            case 'add_exam':
            case 'edit_exam':
                include 'exam_form.php';
                break;
                
            case 'delete_exam':
                include 'delete_exam.php';
                break;
                
            default:
                include 'students_list.php';
                break;
        }
        ?>
    </div>
</body>
</html>