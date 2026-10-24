<?php
require_once __DIR__ . '/JWT.php';
require_once __DIR__ . '/RolePermissions.php';
require_once __DIR__ . '/Response.php';
class AuthMiddleware {
    public static function handle(?string $requiredModule = null, string $action = 'read'): array {
        $token = JWT::fromRequest();

        if (!$token) {
            Response::error('No autorizado. Token requerido.', 401);
        }
        try { 
            $user = JWT::decode($token); 
        } catch (Exception $e) { 
            Response::error($e->getMessage(), 401); 
        }
        
        if ($requiredModule !== null) {
            if (!RolePermissions::can($user['rol'] ?? '', $requiredModule, $action)) {
                Response::error('No tiene permisos para realizar esta acción.', 403);
            }
        }

        return $user;
    }
}
