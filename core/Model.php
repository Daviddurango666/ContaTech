<?php
require_once __DIR__ . '/Database.php';
abstract class Model {
    
    protected PDO $db;
    
    protected string $table;
    
    protected string $primaryKey = 'id';
    
    public function __construct() { 
        $this->db = Database::getInstance(); 
    }
    
    public function findAll(string $orderBy = null): array {
        $sql = "
                SELECT * FROM 
                {$this->table}
                ";

        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }
        
        return $this->db->query($sql)->fetchAll();
    }

    public function findById($id): ?array {
        $stmt = $this->db->prepare("
                                    SELECT * FROM 
                                    {$this->table} 
                                    WHERE 
                                    {$this->primaryKey} = ?
                                ");

        $stmt->execute([$id]);

        $row = $stmt->fetch();
        
        return $row ?: null;
    }
    public function delete($id): bool {
        
        return $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?")->execute([$id]);
    
    }
}
