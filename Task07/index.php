<?php
// index.php - Главный контроллер
require_once 'StudentDatabase.php';

$database = new StudentDatabase('university.db');

// Получаем список групп для фильтра
$activeGroups = $database->getActiveGroups();

// Получаем параметр фильтра
$selectedGroup = $_GET['group'] ?? null;
if ($selectedGroup && !in_array($selectedGroup, $activeGroups)) {
    $selectedGroup = null;
}

// Получаем студентов
$students = $database->getStudents($selectedGroup);

// Подключаем шаблон
require 'template.php';