<?php
require_once __DIR__ . '/../../core/Model.php';
class ProductModel extends Model {
    protected string $table = 'productos';
    public function allWithCategory(): array {
        return $this->db->query("
                                SELECT 
                                p.*,
                                c.nombre AS categoria_nombre 
                                FROM productos p 
                                LEFT JOIN 
                                categorias c ON c.id=p.categoria_id 
                                ORDER BY 
                                p.nombre"
        )->fetchAll();
    }
    public function create(array $d): int {
        $s=$this->db->prepare("
                                INSERT INTO productos 
                                (codigo,nombre,categoria_id,precio,costo,stock,stock_minimo,unidad,activo) 
                                VALUES (?,?,?,?,?,?,?,?,?)
        ");
        $s->execute([
                    $d['codigo'],
                    $d['nombre'],
                    $d['categoria_id']??null,
                    $d['precio']??0,
                    $d['costo']??0,
                    $d['stock']??0,
                    $d['stock_minimo']??5,
                    $d['unidad']??'UND',
                    $d['activo']??1]
        );
        return (int)$this->db->lastInsertId();
    }
    public function updateProduct(int $id, array $d): bool {
        return $this->db->prepare("
                                    UPDATE productos 
                                    SET 
                                    codigo=?,
                                    nombre=?,
                                    categoria_id=?,
                                    precio=?,
                                    costo=?,
                                    stock_minimo=?,
                                    unidad=?,
                                    activo=? 
                                    WHERE 
                                    id=?"
        )->execute([
            $d['codigo'],
            $d['nombre'],
            $d['categoria_id']??null,
            $d['precio']??0,
            $d['costo']??0,
            $d['stock_minimo']??5,
            $d['unidad']??'UND',
            $d['activo']??1,
            $id]
        );
    }
    public function updateStock(int $id, int $qty, string $op='set'): bool {
        if($op==='add'){
            $sql="UPDATE productos SET stock=stock+? WHERE id=?";
        } elseif($op==='sub'){
            $sql="UPDATE productos SET stock=GREATEST(0,stock-?) WHERE id=?";
        } else {
            $sql="UPDATE productos SET stock=? WHERE id=?";
        }
        
        return $this->db->prepare($sql)->execute([$qty,$id]);
    }

    public function lowStock(): array {
        return $this->db->query("
                                    SELECT 
                                    p.*,
                                    c.nombre AS categoria_nombre 
                                    FROM productos p 
                                    LEFT JOIN 
                                    categorias c ON c.id=p.categoria_id 
                                    WHERE 
                                    p.activo=1 AND p.stock<=p.stock_minimo 
                                    ORDER BY 
                                    p.stock"
        )->fetchAll();
    }
}
class CategoryModel extends Model {
    protected string $table = 'categorias';

    public function create(array $d): int {
        $this->db->prepare("
                                INSERT INTO 
                                categorias 
                                (nombre,descripcion,activo) 
                                VALUES (?,?,?)"
        )->execute([
            $d['nombre'],
            $d['descripcion']??null,
            $d['activo']??1]
        );
        return (int)$this->db->lastInsertId();
    }
    public function updateCategory(int $id, array $d): bool {
        return $this->db->prepare("
                                UPDATE categorias 
                                SET 
                                nombre=?,
                                descripcion=?,
                                activo=? 
                                WHERE 
                                id=?"
        )->execute([
            $d['nombre'],
            $d['descripcion']??null,
            $d['activo']??1,
            $id]
        );
    }
}
