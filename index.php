<?php
/**
 * ContaTech 2.0 - Punto de entrada
 * Redirige al login o al dashboard según sesión JWT
 */
$appCfg = require __DIR__ . '/config/app.php';
$base = $appCfg['base_url'];

$token = $_COOKIE['contatech_token'] ?? null;
if ($token) {
    require_once __DIR__ . '/core/JWT.php';
    try {
        JWT::decode($token);
        header('Location: ' . $base . '/views/dashboard/index.php');
        exit;
    } catch (Exception $e) {
        setcookie('contatech_token', '', time() - 3600, '/');
    }
}
header('Location: ' . $base . '/views/auth/login.php');
exit;
