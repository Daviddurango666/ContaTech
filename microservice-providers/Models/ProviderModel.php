<?php
require_once __DIR__ . '/../../core/Model.php';
class ProviderModel extends Model {
    protected string $table = 'proveedores';
    public function create(array $d): int {
        $s=$this->db->prepare("INSERT INTO proveedores (nit,razon_social,contacto,email,telefono,direccion,activo) VALUES (?,?,?,?,?,?,?)");
        $s->execute([$d['nit'],$d['razon_social'],$d['contacto']??null,$d['email']??null,$d['telefono']??null,$d['direccion']??null,$d['activo']??1]);
        return (int)$this->db->lastInsertId();
    }
    public function updateProvider(int $id, array $d): bool {
        return $this->db->prepare("UPDATE proveedores SET nit=?,razon_social=?,contacto=?,email=?,telefono=?,direccion=?,activo=? WHERE id=?")
            ->execute([$d['nit'],$d['razon_social'],$d['contacto']??null,$d['email']??null,$d['telefono']??null,$d['direccion']??null,$d['activo']??1,$id]);
    }
}
