<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../microservice-auth/Models/UserModel.php';
class UserController extends Controller {
    private UserModel $users;
    public function __construct(){parent::__construct();$this->users=new UserModel();}
    public function index(): void {
        AuthMiddleware::handle('usuarios','read');
        $list=$this->users->findAll('nombre ASC');
        $list=array_map(function($u){unset($u['password'],$u['token_reset'],$u['token_reset_expira']);return $u;},$list);
        Response::success($list);
    }
    public function show(int $id): void {
        AuthMiddleware::handle('usuarios','read');
        $u=$this->users->findById($id); if(!$u) Response::error('Usuario no encontrado.',404);
        unset($u['password'],$u['token_reset'],$u['token_reset_expira']); Response::success($u);
    }
    public function store(): void {
        AuthMiddleware::handle('usuarios','create'); $d=$this->input();
        foreach(['nombre','usuario','email','password','rol'] as $f) if(empty($d[$f])) Response::error("Campo $f obligatorio.",422);
        $cfg=require __DIR__.'/../../../config/app.php';
        if(!isset($cfg['roles'][$d['rol']])) Response::error('Rol no válido.',422);
        try{ $id=$this->users->create($d); Response::success(['id'=>$id],'Usuario creado.',201); }
        catch(PDOException $e){ Response::error('Email/usuario duplicado.',409); }
    }
    public function update(int $id): void {
        AuthMiddleware::handle('usuarios','update');
        if(!$this->users->findById($id)) Response::error('Usuario no encontrado.',404);
        $this->users->updateUser($id,$this->input()); Response::success(null,'Usuario actualizado.');
    }
    public function destroy(int $id): void {
        AuthMiddleware::handle('usuarios','delete');
        $auth=AuthMiddleware::handle();
        if((int)$auth['id']===$id) Response::error('No puede eliminarse a sí mismo.',400);
        $this->users->updateUser($id,['activo'=>0]); Response::success(null,'Usuario desactivado.');
    }
}
