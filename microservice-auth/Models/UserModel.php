<?php
require_once __DIR__ . '/../../core/Model.php';

class UserModel extends Model {

    protected string $table = 'usuarios';
    
    public function findByLogin(string $login): ?array {
        $s = $this->db->prepare("
                                SELECT * FROM usuarios 
                                WHERE 
                                (email=? OR usuario=?) 
                                AND 
                                activo=1 LIMIT 1"
                                );

        $s->execute([$login,$login]); 
        $r=$s->fetch(); 
        
        return $r?:null;
    }
    
    public function findByEmail(string $email): ?array 
    {
        $s=$this->db->prepare("
                                SELECT * FROM usuarios 
                                WHERE 
                                email=? 
                                LIMIT 1"
                            ); 

        $s->execute([$email]); 
        $r=$s->fetch(); 
        
        return $r?:null;
    }
    
    public function create(array $d): int {
        
        $s=$this->db->prepare("
                                INSERT INTO usuarios 
                                (nombre,usuario,email,password,rol,activo) 
                                VALUES (?,?,?,?,?,?)"
                            );
        
        $s->execute([
            $d['nombre'],
            $d['usuario'],
            $d['email'],
            password_hash($d['password'],PASSWORD_BCRYPT),
            $d['rol']??'cajero',
            $d['activo']??1
        ]);
        
        return (int)$this->db->lastInsertId();
    }
    
    public function updateUser(int $id, array $d): bool {
        
        $f=[]; 
        $p=[];
        
        foreach (['nombre','usuario','email','rol','activo'] as $k) {
            if(array_key_exists($k,$d)){
                $f[]="$k=?";
                $p[]=$d[$k];
            }
        }
        
        if(!empty($d['password'])){
            $f[]="password=?";
            $p[]=password_hash($d['password'],PASSWORD_BCRYPT);
        }

        if(!$f){
            return false; 
            $p[]=$id;
        }
        
        return $this->db->prepare("UPDATE usuarios SET ".implode(',',$f)." WHERE id=?")->execute($p);
    }
    
    public function setResetToken(int $id, string $t, string $e): bool {
        return $this->db->prepare("UPDATE usuarios SET token_reset=?, token_reset_expira=? WHERE id=?")->execute([$t,$e,$id]);
    }
    
    public function findByResetToken(string $t): ?array {
        $s=$this->db->prepare("
                                SELECT * FROM usuarios 
                                WHERE 
                                token_reset=? 
                                AND 
                                token_reset_expira>NOW() 
                                LIMIT 1"
                            );
        
        $s->execute([$t]); 
        $r=$s->fetch(); 
        
        return $r?:null;
    }
    
    public function clearResetToken(int $id): void {
        $this->db->prepare("UPDATE usuarios SET token_reset=NULL, token_reset_expira=NULL WHERE id=?")->execute([$id]);
    }
}
