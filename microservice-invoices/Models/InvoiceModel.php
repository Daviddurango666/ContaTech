<?php
require_once __DIR__ . '/../../core/Model.php';
class InvoiceModel extends Model {
    protected string $table = 'facturas';

    public function nextNumber(): string {
        $y=date('Y'); 
        $c=(int)$this->db->query("
                                    SELECT COUNT(*) c 
                                    FROM facturas 
                                    WHERE 
                                    YEAR(created_at)=$y"
        )->fetch()['c']+1;

        return sprintf('FE-%s-%05d',$y,$c);
    }

    public function createFromOrder(array $h, array $items): int {
        $this->db->beginTransaction();
        try{
            $s=$this->db->prepare("
                                    INSERT INTO facturas 
                                    (numero,orden_id,cliente_id,usuario_id,fecha,subtotal,impuestos,total,estado) 
                                    VALUES 
                                    (?,?,?,?,?,?,?,?,?)
            ");

            $s->execute([
                $h['numero'],
                $h['orden_id']??null,
                $h['cliente_id']??null,
                $h['usuario_id'],
                $h['fecha'],
                $h['subtotal'],
                $h['impuestos'],
                $h['total'],
                $h['estado']??'emitida'
            ]);

            $fid=(int)$this->db->lastInsertId();

            $det=$this->db->prepare("
                                        INSERT INTO facturas_detalle 
                                        (factura_id,producto_id,descripcion,cantidad,precio_unitario,subtotal) 
                                        VALUES 
                                        (?,?,?,?,?,?)
            ");

            foreach($items as $it) {
                $det->execute([
                    $fid,
                    $it['producto_id']??null,
                    $it['descripcion'],
                    $it['cantidad'],
                    $it['precio_unitario'],
                    $it['subtotal']
                ]);
            }
            $this->db->commit(); 
            return $fid;
        }catch(Exception $e){ 
            $this->db->rollBack(); 
            throw $e; 
        }
    }
    public function allWithRelations(): array {
        return $this->db->query("
                                SELECT 
                                f.*,
                                c.nombre AS cliente_nombre,
                                u.nombre AS usuario_nombre 
                                FROM facturas f 
                                LEFT JOIN clientes c ON c.id=f.cliente_id 
                                LEFT JOIN usuarios u ON u.id=f.usuario_id 
                                ORDER BY 
                                f.created_at DESC
        ")->fetchAll();
    }
    public function findWithDetails(int $id): ?array {
        $s = $this->db->prepare("
            SELECT f.*,
                c.nombre AS cliente_nombre,
                c.documento AS cliente_documento,
                c.tipo_doc AS cliente_tipo_doc,
                u.nombre AS usuario_nombre
            FROM facturas f
            LEFT JOIN clientes c ON c.id = f.cliente_id
            LEFT JOIN usuarios u ON u.id = f.usuario_id
            WHERE f.id = ?
        ");
        $s->execute([$id]);
        $f = $s->fetch();
        if (!$f) return null;

        $d = $this->db->prepare("SELECT * FROM facturas_detalle WHERE factura_id = ?");
        $d->execute([$id]);
        $f['detalle'] = $d->fetchAll();
        return $f;
    }
    public function markAccounting(int $id): void {
        $f=$this->findById($id); 
        
        if(!$f||$f['registro_contable']){
            return;
        } 
        $this->db->prepare("
                            INSERT INTO asientos_contables 
                            (factura_id,fecha,cuenta_debito,cuenta_credito,monto,descripcion) 
                            VALUES 
                            (?,?,?,?,?,?)"
        )->execute([
            $id,
            $f['fecha'],
            '1105-Caja/Bancos',
            '4135-Comercio',
            $f['total'],
            'Registro factura '.$f['numero']
        ]);
        $this->db->prepare("
                            UPDATE facturas 
                            SET 
                            registro_contable=1 
                            WHERE 
                            id=?"
        )->execute([$id]);
    }
    public function updateDian(int $id, string $estado, ?string $cufe, ?string $resp): void {
        $this->db->prepare("
                            UPDATE facturas 
                            SET 
                            estado=?,
                            cufe=?,
                            dian_response=? 
                            WHERE 
                            id=?"
        )->execute([
            $estado,
            $cufe,
            $resp,
            $id
        ]);
    }
}
