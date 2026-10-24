<?php
require_once __DIR__ . '/../../core/Model.php';
class OrderModel extends Model {
    protected string $table = 'ordenes_venta';
    public function nextNumber(): string {
        $y=date('Y'); 
        $c=(int)$this->db->query("
                                    SELECT COUNT(*) c 
                                    FROM ordenes_venta 
                                    WHERE 
                                    YEAR(created_at)=$y
        ")->fetch()['c']+1;

        return sprintf(
            'OV-%s-%05d',
            $y,
            $c
        );
    }
    public function createWithItems(array $h, array $items): int {
        $this->db->beginTransaction();
        
        try {
            $s=$this->db->prepare("
                                    INSERT INTO ordenes_venta 
                                    (numero,cliente_id,usuario_id,estado,subtotal,impuestos,total,observaciones) 
                                    VALUES 
                                    (?,?,?,?,?,?,?,?)
            ");

            $s->execute([
                $h['numero'],
                $h['cliente_id']??null,
                $h['usuario_id'],
                $h['estado']??'pendiente',
                $h['subtotal'],
                $h['impuestos'],
                $h['total'],
                $h['observaciones']??null
            ]);

            $oid=(int)$this->db->lastInsertId();
            
            $det=$this->db->prepare("
                                    INSERT INTO ordenes_venta_detalle 
                                    (orden_id,producto_id,cantidad,precio_unitario,subtotal) 
                                    VALUES 
                                    (?,?,?,?,?)
            ");
            
            $stock=$this->db->prepare("
                                    UPDATE productos 
                                    SET 
                                    stock=GREATEST(0,stock-?) 
                                    WHERE 
                                    id=?
            ");
            
            foreach($items as $it){ 
                $det->execute([
                    $oid,
                    $it['producto_id'],
                    $it['cantidad'],
                    $it['precio_unitario'],
                    $it['subtotal']
                ]); 
                $stock->execute([
                    $it['cantidad'],
                    $it['producto_id']
                    ]); 
            }
            
            $this->db->commit(); 
            return $oid;
        } catch(Exception $e){ 
            $this->db->rollBack(); 
            throw $e; 
        }
    }
    public function allWithRelations(): array {
        return $this->db->query("
                                SELECT 
                                o.*,
                                c.nombre AS cliente_nombre,
                                u.nombre AS usuario_nombre 
                                FROM ordenes_venta o 
                                LEFT JOIN clientes c ON c.id=o.cliente_id 
                                LEFT JOIN usuarios u ON u.id=o.usuario_id 
                                ORDER BY 
                                o.created_at DESC
        ")->fetchAll();
    }
    public function findWithDetails(int $id): ?array {
        $o=$this->findById($id); 
        if(!$o){
            return null;
        }
        $s=$this->db->prepare("
                                SELECT 
                                d.*,
                                p.nombre AS producto_nombre,
                                p.codigo 
                                FROM ordenes_venta_detalle d 
                                JOIN productos p ON p.id=d.producto_id 
                                WHERE 
                                d.orden_id=?
        ");
        $s->execute([$id]); 
        $o['detalle']=$s->fetchAll(); 
        return $o;
    }
    public function updateStatus(int $id, string $estado): bool {
        if(!in_array(
            $estado,
            [
                'pendiente',
                'facturado',
                'cobrado',
                'anulado'
            ],
        true)) {
            return false;
        }
        return $this->db->prepare("
                                    UPDATE ordenes_venta 
                                    SET 
                                    estado=? 
                                    WHERE 
                                    id=?
        ")->execute([$estado,$id]);
    }
}
