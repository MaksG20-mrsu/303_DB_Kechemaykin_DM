<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список студентов - Лабораторная работа 7</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .subtitle {
            font-size: 1.2em;
            opacity: 0.9;
        }
        
        .content {
            padding: 30px;
        }
        
        .filters {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-width: 250px;
        }
        
        label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #555;
        }
        
        select, button {
            padding: 10px 15px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        select {
            background-color: white;
            cursor: pointer;
        }
        
        select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        button {
            background-color: #667eea;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 600;
            align-self: flex-end;
            min-width: 120px;
        }
        
        button:hover {
            background-color: #5a67d8;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .stats {
            background-color: #e8f4fd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .student-count {
            font-size: 1.1em;
            font-weight: 600;
            color: #2c5282;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 1.2em;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        thead {
            background-color: #4a5568;
            color: white;
        }
        
        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            border-bottom: 3px solid #2d3748;
        }
        
        tbody tr {
            border-bottom: 1px solid #e2e8f0;
            transition: background-color 0.2s ease;
        }
        
        tbody tr:hover {
            background-color: #f7fafc;
        }
        
        td {
            padding: 15px;
            vertical-align: top;
        }
        
        .group-badge {
            display: inline-block;
            background-color: #667eea;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9em;
        }
        
        .gender-male {
            color: #3182ce;
            font-weight: 600;
        }
        
        .gender-female {
            color: #d53f8c;
            font-weight: 600;
        }
        
        @media (max-width: 768px) {
            .container {
                border-radius: 0;
            }
            
            .filters {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-group {
                min-width: 100%;
            }
            
            table {
                display: block;
                overflow-x: auto;
            }
            
            th, td {
                min-width: 120px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Список студентов</h1>
            <div class="subtitle">Лабораторная работа №7 - Веб-приложение для отображения данных из БД</div>
        </header>
        
        <div class="content">
            <!-- Форма фильтрации -->
            <form method="GET" action="" class="filters">
                <div class="filter-group">
                    <label for="group">Фильтр по группе:</label>
                    <select name="group" id="group">
                        <option value="">Все группы</option>
                        <?php if (!empty($activeGroups)): ?>
                            <?php foreach ($activeGroups as $group): ?>
                                <option value="<?= htmlspecialchars($group) ?>" 
                                    <?= ($selectedGroup === $group) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($group) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <button type="submit">Применить фильтр</button>
            </form>
            
            <!-- Статистика -->
            <div class="stats">
                <div>
                    <?php if ($selectedGroup): ?>
                        <h2>Группа: <span class="group-badge"><?= htmlspecialchars($selectedGroup) ?></span></h2>
                    <?php else: ?>
                        <h2>Все действующие группы</h2>
                    <?php endif; ?>
                </div>
                <div class="student-count">
                    Всего студентов: <?= count($students) ?>
                </div>
            </div>
            
            <!-- Таблица студентов -->
            <?php if (empty($students)): ?>
                <div class="no-data">
                    <p>Нет данных о студентах для отображения</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>№ группы</th>
                            <th>Направление подготовки</th>
                            <th>ФИО студента</th>
                            <th>Пол</th>
                            <th>Дата рождения</th>
                            <th>Номер студ. билета</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td>
                                    <span class="group-badge"><?= htmlspecialchars($student['group_number']) ?></span>
                                </td>
                                <td><?= htmlspecialchars($student['specialization']) ?></td>
                                <td><?= htmlspecialchars($student['full_name']) ?></td>
                                <td>
                                    <?php if ($student['gender'] === 'Мужской'): ?>
                                        <span class="gender-male"><?= htmlspecialchars($student['gender']) ?></span>
                                    <?php else: ?>
                                        <span class="gender-female"><?= htmlspecialchars($student['gender']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($student['birth_date']) ?></td>
                                <td><strong><?= htmlspecialchars($student['student_id']) ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>