<?php
require_once __DIR__ . '/../../core/JWT.php';
require_once __DIR__ . '/../../core/RolePermissions.php';

$token = $_COOKIE['contatech_token'] ?? null;

if (!$token) { 
    header('Location: /ContaTech1/views/auth/login.php'); 
    exit; 
}

try { 
    $currentUser = JWT::decode($token); 
} catch (Exception $e) { 
    setcookie('contatech_token','',time()-3600,'/'); 
    header('Location: /ContaTech/views/auth/login.php'); 
    exit; 
}

$menuItems = RolePermissions::menuFor($currentUser['rol'] ?? '');

$appCfg = require __DIR__ . '/../../config/app.php';
