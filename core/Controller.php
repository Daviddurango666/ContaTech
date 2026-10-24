<?php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Response.php';
require_once __DIR__ . '/AuthMiddleware.php';

abstract class Controller {

    protected PDO $db;
    public function __construct() { 
        $this->db = Database::getInstance(); 
    }
    protected function input(): array {
        
        $ct = $_SERVER['CONTENT_TYPE'] ?? '';
        
        if (stripos($ct, 'application/json') !== false) {
            $data = json_decode(file_get_contents('php://input'), true);
            return is_array($data) ? $data : [];
        }
        
        return array_merge($_GET, $_POST);
    }
}
