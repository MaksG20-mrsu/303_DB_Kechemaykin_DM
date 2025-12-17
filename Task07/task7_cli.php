<?php
/**
 * Лабораторная работа 7 - Консольное приложение
 * Вывод списка студентов с возможностью фильтрации по группам
 */

class StudentDatabase
{
    private PDO $db;

    public function __construct(string $databasePath)
    {
        try {
            $this->db = new PDO("sqlite:$databasePath");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Ошибка подключения к базе данных: " . $e->getMessage());
        }
    }

    /**
     * Получение списка действующих групп
     */
    public function getActiveGroups(): array
    {
        $currentYear = date('Y');
        $query = "SELECT DISTINCT group_number 
                  FROM groups 
                  WHERE graduation_year >= :currentYear 
                  ORDER BY group_number";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute(['currentYear' => $currentYear]);
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Получение студентов с возможностью фильтрации по группе
     */
    public function getStudents(?string $groupFilter = null): array
    {
        $currentYear = date('Y');
        $params = ['currentYear' => $currentYear];
        
        $query = "SELECT 
                    g.group_number,
                    g.specialization,
                    s.last_name || ' ' || s.first_name || ' ' || COALESCE(s.middle_name, '') as full_name,
                    CASE s.gender 
                        WHEN 'M' THEN 'Мужской' 
                        WHEN 'F' THEN 'Женский' 
                    END as gender,
                    s.birth_date,
                    s.student_id
                  FROM students s
                  JOIN groups g ON s.group_id = g.id
                  WHERE g.graduation_year >= :currentYear";
        
        if ($groupFilter) {
            $query .= " AND g.group_number = :groupNumber";
            $params['groupNumber'] = $groupFilter;
        }
        
        $query .= " ORDER BY g.group_number, s.last_name, s.first_name";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

/**
 * Функция для отображения таблицы в псевдографике
 */
function displayTable(array $headers, array $data): void
{
    // Вычисление максимальных ширин столбцов
    $columnWidths = array_map('mb_strlen', $headers);
    
    foreach ($data as $row) {
        foreach ($row as $index => $value) {
            $width = mb_strlen((string)$value);
            if ($width > $columnWidths[$index]) {
                $columnWidths[$index] = $width;
            }
        }
    }
    
    // Добавляем отступы
    $columnWidths = array_map(fn($w) => $w + 2, $columnWidths);
    
    // Верхняя граница таблицы
    echo "+";
    foreach ($columnWidths as $width) {
        echo str_repeat("-", $width) . "+";
    }
    echo "\n";
    
    // Заголовки
    echo "|";
    foreach ($headers as $index => $header) {
        printf(" %-".($columnWidths[$index]-1)."s|", $header);
    }
    echo "\n";
    
    // Разделитель заголовков
    echo "+";
    foreach ($columnWidths as $width) {
        echo str_repeat("-", $width) . "+";
    }
    echo "\n";
    
    // Данные
    if (empty($data)) {
        // Пустая строка, если данных нет
        echo "|";
        $totalWidth = array_sum($columnWidths) + count($columnWidths) - 1;
        printf(" %-".($totalWidth-1)."s|", "Нет данных");
        echo "\n";
    } else {
        foreach ($data as $row) {
            echo "|";
            foreach ($row as $index => $value) {
                printf(" %-".($columnWidths[$index]-1)."s|", $value);
            }
            echo "\n";
        }
    }
    
    // Нижняя граница таблицы
    echo "+";
    foreach ($columnWidths as $width) {
        echo str_repeat("-", $width) . "+";
    }
    echo "\n";
}

/**
 * Основная функция консольного приложения
 */
function main(): void
{
    $database = new StudentDatabase('university.db');
    
    // Получаем список действующих групп
    $activeGroups = $database->getActiveGroups();
    
    if (empty($activeGroups)) {
        echo "Нет действующих групп в базе данных.\n";
        return;
    }
    
    // Вывод доступных групп
    echo "Доступные группы:\n";
    foreach ($activeGroups as $group) {
        echo "  - $group\n";
    }
    echo "\n";
    
    // Запрос выбора группы у пользователя
    echo "Введите номер группы для фильтрации (или нажмите Enter для всех групп): ";
    $input = trim(fgets(STDIN));
    
    // Валидация ввода
    $selectedGroup = null;
    if (!empty($input)) {
        if (in_array($input, $activeGroups)) {
            $selectedGroup = $input;
        } else {
            echo "Ошибка: Группа '$input' не найдена в списке действующих групп.\n";
            return;
        }
    }
    
    // Получение данных
    $students = $database->getStudents($selectedGroup);
    
    // Подготовка данных для отображения
    $tableHeaders = [
        'Группа',
        'Направление',
        'ФИО',
        'Пол',
        'Дата рождения',
        'Номер билета'
    ];
    
    $tableData = [];
    foreach ($students as $student) {
        $tableData[] = [
            $student['group_number'],
            $student['specialization'],
            $student['full_name'],
            $student['gender'],
            $student['birth_date'],
            $student['student_id']
        ];
    }
    
    // Отображение результатов
    if ($selectedGroup) {
        echo "\nСтуденты группы '$selectedGroup':\n";
    } else {
        echo "\nВсе студенты действующих групп:\n";
    }
    
    displayTable($tableHeaders, $tableData);
    
    // Статистика
    echo "\nВсего студентов: " . count($students) . "\n";
}

// Запуск приложения
if (PHP_SAPI === 'cli') {
    main();
} else {
    die("Этот скрипт предназначен только для запуска из командной строки.");
}