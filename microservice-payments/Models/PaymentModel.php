<?php
require_once __DIR__ . '/../../core/Model.php';
class PaymentModel extends Model {
    protected string $table = 'pagos';
    public function create(array $d): int {
        $s=$this->db->prepare("INSERT INTO pagos (factura_id,orden_id,metodo_pago_id,monto,monto_factura,referencia,bold_payment_id,estado,conciliado,usuario_id) VALUES (?,?,?,?,?,?,?,?,?,?)");
        $s->execute([$d['factura_id']??null,$d['orden_id']??null,$d['metodo_pago_id'],$d['monto'],$d['monto_factura']??null,$d['referencia']??null,$d['bold_payment_id']??null,$d['estado']??'pendiente',$d['conciliado']??0,$d['usuario_id']??null]);
        return (int)$this->db->lastInsertId();
    }
    public function allWithRelations(): array {
        return $this->db->query("SELECT p.*,m.nombre AS metodo_nombre,m.tipo AS metodo_tipo,f.numero AS factura_numero,f.total AS factura_total FROM pagos p JOIN metodos_pago m ON m.id=p.metodo_pago_id LEFT JOIN facturas f ON f.id=p.factura_id ORDER BY p.created_at DESC")->fetchAll();
    }
    public function methods(): array { return $this->db->query("SELECT * FROM metodos_pago WHERE activo=1")->fetchAll(); }
    public function conciliate(int $id): array {
        $p=$this->findById($id); if(!$p) throw new Exception('Pago no encontrado');
        $montoPago=(float)$p['monto']; $montoFac=(float)($p['monto_factura']??0);
        if($p['factura_id']&&$montoFac<=0){ $f=$this->db->prepare("SELECT total FROM facturas WHERE id=?"); $f->execute([$p['factura_id']]); $row=$f->fetch(); $montoFac=$row?(float)$row['total']:0; }
        $ok=abs($montoPago-$montoFac)<0.01;
        if($ok){
            $this->db->prepare("UPDATE pagos SET estado='conciliado',conciliado=1,monto_factura=? WHERE id=?")->execute([$montoFac,$id]);
            if($p['orden_id']) $this->db->prepare("UPDATE ordenes_venta SET estado='cobrado' WHERE id=?")->execute([$p['orden_id']]);
        }
        return ['conciliado'=>$ok,'monto_pago'=>$montoPago,'monto_factura'=>$montoFac,'diferencia'=>round($montoPago-$montoFac,2)];
    }
}
