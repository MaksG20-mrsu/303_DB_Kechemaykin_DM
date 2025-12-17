<?php
/**
 * StudentDatabase.php - Класс для работы с базой данных
 */
class StudentDatabase
{
    private PDO $db;

    public function __construct(string $databasePath)
    {
        try {
            $this->db = new PDO("sqlite:$databasePath");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Включаем поддержку UTF-8
            $this->db->exec("PRAGMA encoding = 'UTF-8'");
        } catch (PDOException $e) {
            die("Ошибка подключения к базе данных: " . $e->getMessage());
        }
    }

    public function getActiveGroups(): array
    {
        $currentYear = date('Y');
        $query = "SELECT DISTINCT group_number 
                  FROM groups 
                  WHERE graduation_year >= :currentYear 
                  ORDER BY group_number";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute(['currentYear' => $currentYear]);
        
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $result ?: [];
    }

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
        
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result ?: [];
    }
}