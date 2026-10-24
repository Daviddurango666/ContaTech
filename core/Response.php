<?php
class Response {
    public static function json($data, int $status = 200): void {
        
        http_response_code($status);
        
        header('Content-Type: application/json; charset=utf-8');
        
        header('Access-Control-Allow-Origin: *');
        
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
        exit;
    }

    public static function success($data = null, string $message = 'OK', int $status = 200): void {
    
        self::json([
            'success' => true, 
            'message' => $message, 
            'data' => $data], 
            $status
        );
    
    }

    public static function error(string $message, int $status = 400, $errors = null): void {
    
        self::json([
            'success' => false, 
            'message' => $message, 
            'errors' => $errors], 
            $status
        );
    
    }
}
