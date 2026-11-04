<?php
require_once __DIR__ . '/../../core/Model.php';

class PurchaseModel extends Model {

    
    protected string $table = 'ordenes_compra';
    
    public function nextNumber(): string {
        $y=date('Y'); 
        $c=(int)$this->db->query("
                                    SELECT COUNT(*) c FROM ordenes_compra 
                                    WHERE 
                                    YEAR(created_at)=$y
                                ")->fetch()['c']+1;

        return sprintf('OC-%s-%05d',$y,$c);
    }
    
    public function createWithItems(array $h, array $items): int {
        
        $this->db->beginTransaction();
        
        try{
        
            $s=$this->db->prepare("
                                    INSERT INTO ordenes_compra 
                                    (numero,proveedor_id,usuario_id,estado,subtotal,total,observaciones) 
                                    VALUES 
                                    (?,?,?,?,?,?,?)
                                ");
            
            $s->execute([
                $h['numero'],
                $h['proveedor_id'],
                $h['usuario_id'],
                $h['estado']??'solicitado',
                $h['subtotal'],
                $h['total'],
                $h['observaciones']??null
            ]);
            
            $id=(int)$this->db->lastInsertId();
            
            $det=$this->db->prepare("
                                    INSERT INTO ordenes_compra_detalle 
                                    (orden_id,producto_id,cantidad,costo_unitario,subtotal) 
                                    VALUES 
                                    (?,?,?,?,?)
                                ");
            
            foreach($items as $it) {
                
                $det->execute([
                    $id,
                    $it['producto_id'],
                    $it['cantidad'],
                    $it['costo_unitario'],
                    $it['subtotal']
                ]);
            }
            
            $this->db->commit(); 
            return $id;
        
        } catch(Exception $e){ 
            $this->db->rollBack(); 
            throw $e; 
        }
    }
    
    public function allWithRelations(): array {
        return $this->db->query("
                                SELECT 
                                o.*,
                                p.razon_social AS proveedor_nombre,
                                u.nombre AS usuario_nombre 
                                FROM ordenes_compra o 
                                JOIN proveedores p ON p.id=o.proveedor_id 
                                JOIN usuarios u ON u.id=o.usuario_id 
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
                                pr.nombre AS producto_nombre,
                                pr.codigo 
                                FROM ordenes_compra_detalle d 
                                JOIN productos pr ON pr.id=d.producto_id 
                                WHERE 
                                d.orden_id=?
                            ");

        $s->execute([$id]); 
        $o['detalle']=$s->fetchAll(); 
        return $o;
    }
    
    public function updateStatus(int $id, string $estado, ?int $usuarioId = null): bool {
        if (!in_array($estado, ['solicitado', 'facturado_proveedor', 'entregado', 'anulado'], true)) {
            return false;
        }

        $actual = $this->findById($id);
        if (!$actual) {
            return false;
        }

        // Evitar procesar dos veces una OC ya entregada
        if ($estado === 'entregado' && $actual['estado'] === 'entregado') {
            return true;
        }

        $this->db->beginTransaction();

        try {
            $this->db->prepare("
                UPDATE ordenes_compra
                SET estado = ?
                WHERE id = ?
            ")->execute([$estado, $id]);

            if ($estado === 'entregado') {
                $o = $this->findWithDetails($id);

                if (!$o || empty($o['detalle'])) {
                    throw new Exception('OC sin detalle');
                }

                // Sube stock y actualiza costo unitario del producto
                $st = $this->db->prepare("
                    UPDATE productos
                    SET stock = stock + ?,
                        costo = ?
                    WHERE id = ?
                ");

                $totalCosto = 0.0;

                foreach ($o['detalle'] as $it) {
                    $cant  = (int)$it['cantidad'];
                    $costo = (float)$it['costo_unitario'];
                    $totalCosto += $cant * $costo;

                    $st->execute([
                        $cant,
                        $costo,
                        $it['producto_id']
                    ]);
                }

                // Registrar EGRESO en movimientos (solo una vez por OC)
                if ($totalCosto > 0) {
                    $ref = 'EGRESO-OC-' . $id;

                    $existe = $this->db->prepare("
                        SELECT id FROM movimientos
                        WHERE referencia = ?
                        LIMIT 1
                    ");
                    $existe->execute([$ref]);

                    if (!$existe->fetch()) {
                        $this->db->prepare("
                            INSERT INTO movimientos
                            (tipo, descripcion, monto, categoria, fecha, usuario_id, referencia)
                            VALUES
                            ('egreso', ?, ?, 'compras', ?, ?, ?)
                        ")->execute([
                            'Compra mercancía ' . ($o['numero'] ?? ('OC-' . $id)),
                            $totalCosto,
                            date('Y-m-d'),
                            $usuarioId ?? $o['usuario_id'] ?? null,
                            $ref,
                        ]);
                    }
                }
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
