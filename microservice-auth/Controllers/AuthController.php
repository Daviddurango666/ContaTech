<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/JWT.php';
require_once __DIR__ . '/../../core/RolePermissions.php';
require_once __DIR__ . '/../Models/UserModel.php';
class AuthController extends Controller {
    private UserModel $users;

    public function __construct(){
        parent::__construct();
        $this->users=new UserModel();
    }
    public function login(): void {
        $d=$this->input(); 
        
        $login=trim($d['login']??$d['email']??$d['usuario']??''); 
        
        $password=$d['password']??'';
        
        if($login===''||$password==='') {
            Response::error('Usuario/email y contraseña son obligatorios.',422);
        }
        
        $user=$this->users->findByLogin($login);

        if(!$user||!password_verify($password,$user['password'])){ 
            Response::error('Credenciales incorrectas.',401);
        }
        $payload=[
                'id'=>(int)$user['id'],
                'nombre'=>$user['nombre'],
                'email'=>$user['email'],
                'usuario'=>$user['usuario'],
                'rol'=>$user['rol']
        ];

        $token=JWT::encode($payload);
        Response::success(
            ['token'=>$token,
            'user'=>$payload,
            'menu'=>RolePermissions::menuFor($user['rol']),
            'expires_in'=>(
                require __DIR__.'/../../../config/app.php'
            )
            ['jwt_expire']],
            'Inicio de sesión exitoso'
        );
    }

    public function logout(): void { 
        Response::success(null,'Sesión cerrada'); 
    }
    
    public function me(): void {
        $user=AuthMiddleware::handle();
        Response::success(
            ['user'=>$user,
            'menu'=>RolePermissions::menuFor(
                $user['rol']??''
            )]
        );
    }
    public function forgotPassword(): void {
        
        $email=trim($this->input()['email']??'');
        
        if($email==='') {
            Response::error('Email requerido.',422);
        }

        $user=$this->users->findByEmail($email);
        
        if($user){
            $token=bin2hex(random_bytes(32)); 
            $exp=date('Y-m-d H:i:s',time()+3600);
            $this->users->setResetToken((int)$user['id'],$token,$exp);
            Response::success(
                [
                    'reset_token'=>$token,
                    'message_dev'=>'Use este token en reset-password (solo desarrollo)'
                ],
                'Si el email existe, se envió enlace de recuperación.'
            );
        }
        
        Response::success(null,'Si el email existe, se envió enlace de recuperación.');
    }
    public function resetPassword(): void {
        
        $d=$this->input(); 
        $token=$d['token']??''; 
        $password=$d['password']??'';
        
        if($token===''||strlen($password)<6){ 
            Response::error('Token y contraseña (mín. 6) requeridos.',422);
        }
        
        $user=$this->users->findByResetToken($token);
        
        if(!$user){
            Response::error('Token inválido o expirado.',400);
        }

        $this->users->updateUser(
            (int)$user['id'],
            ['password'=>$password]
        );
        
        $this->users->clearResetToken(
            (int)$user['id']
        );
        
        Response::success(null,'Contraseña actualizada correctamente.');
    }
    public function changePassword(): void {

        $auth=AuthMiddleware::handle(); $d=$this->input();
        
        $user=$this->users->findById($auth['id']);
        
        if(!$user||!password_verify($d['current_password']??'',$user['password'])){
            Response::error('Contraseña actual incorrecta.',400);
        } 

        if(strlen($d['new_password']??'')<6) {
            Response::error('Nueva contraseña mín. 6 caracteres.',422);
        }
            
        $this->users->updateUser(
            (int)$auth['id'],
            ['password'=>$d['new_password']]
        );
        
        Response::success(null,'Contraseña cambiada.');
    }
}
