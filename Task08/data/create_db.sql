-- Группы
CREATE TABLE IF NOT EXISTS groups (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    group_number VARCHAR(10) NOT NULL UNIQUE
);

INSERT INTO groups (group_number) VALUES 
('ИС-101'),
('ИС-102'),
('ПИ-201'),
('ПИ-202');

-- Студенты
CREATE TABLE IF NOT EXISTS students (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    last_name VARCHAR(50) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    group_id INTEGER NOT NULL,
    gender CHAR(1) NOT NULL CHECK(gender IN ('M', 'F')),
    FOREIGN KEY (group_id) REFERENCES groups(id)
);

INSERT INTO students (last_name, first_name, group_id, gender) VALUES
('Смирнов', 'Александр', 1, 'M'),
('Козлова', 'Екатерина', 1, 'F'),
('Морозов', 'Ильдар', 2, 'M');

-- Дисциплины
CREATE TABLE IF NOT EXISTS disciplines (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    course INTEGER NOT NULL
);

INSERT INTO disciplines (name, course) VALUES
('Математика', 1),
('Программирование', 1),
('Базы данных', 2),
('Веб-разработка', 2);

-- Результаты экзаменов
CREATE TABLE IF NOT EXISTS exam_results (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    student_id INTEGER NOT NULL,
    discipline_id INTEGER NOT NULL,
    exam_date DATE NOT NULL,
    grade INTEGER NOT NULL CHECK(grade BETWEEN 2 AND 5),
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (discipline_id) REFERENCES disciplines(id)
);

INSERT INTO exam_results (student_id, discipline_id, exam_date, grade) VALUES
(1, 1, '2024-01-15', 5),
(1, 2, '2024-01-20', 4),
(2, 1, '2024-01-16', 5);