<?php

require_once __DIR__ . '/../../core/Model.php';

class PaymentModel extends Model {
    
    protected string $table = 'pagos';

    public function create(array $d): int {

        $s=$this->db->prepare("
                                INSERT INTO pagos 
                                (factura_id,orden_id,metodo_pago_id,monto,monto_factura,referencia,bold_payment_id,estado,conciliado,usuario_id) 
                                VALUES 
                                (?,?,?,?,?,?,?,?,?,?)
                            ");
        
        $s->execute([
            $d['factura_id']??null,
            $d['orden_id']??null,
            $d['metodo_pago_id'],
            $d['monto'],
            $d['monto_factura']??null,
            $d['referencia']??null,
            $d['bold_payment_id']??null,
            $d['estado']??'pendiente',
            $d['conciliado']??0,
            $d['usuario_id']??null
        ]);
        
        return (int)$this->db->lastInsertId();
    }

    public function allWithRelations(): array {
        return $this->db->query("
                                SELECT 
                                p.*,
                                m.nombre AS metodo_nombre,
                                m.tipo AS metodo_tipo,
                                f.numero AS factura_numero,
                                f.total AS factura_total 
                                FROM pagos p 
                                JOIN metodos_pago m ON m.id=p.metodo_pago_id 
                                LEFT JOIN facturas f ON f.id=p.factura_id 
                                ORDER BY 
                                p.id DESC
                            ")->fetchAll();
    }

    public function methods(): array { 
        return $this->db->query("
                                SELECT * FROM metodos_pago 
                                WHERE 
                                activo=1
                            ")->fetchAll(); 
    }

    public function conciliate(int $id): array
    {
        $p = $this->findById($id);
        
        if (!$p) {
            throw new Exception('Pago no encontrado');
        }

        // Si ya está conciliado, no hacemos nada
        if (($p['conciliado'] ?? 0) === 1) {
            return [
                'conciliado' => true,
                'mensaje' => 'El pago ya estaba conciliado'
            ];
        }

        // =====================================================
        // 1. Actualizar el pago a CONCILIADO
        // =====================================================
        $stmt = $this->db->prepare("
            UPDATE pagos 
            SET estado = 'conciliado'
            WHERE id = ?
        ");
        $stmt->execute([$id]);

        // =====================================================
        // 2. Generar el INGRESO
        // =====================================================
        $ordenId     = $p['orden_id'] ?? null;
        $monto       = (float)($p['monto'] ?? 0);
        $numeroOrden = $p['numero_orden'] ?? $ordenId ?? $id;
        $usuarioId   = $p['usuario_id'] ?? ($_SESSION['user_id'] ?? null);

        if ($monto > 0) {
            $this->db->prepare("
                INSERT INTO movimientos
                (tipo, descripcion, monto, categoria, fecha, usuario_id, referencia)
                VALUES
                ('ingreso', ?, ?, 'ventas', CURDATE(), ?, ?)
            ")->execute([
                'Ingreso por venta - Orden ' . $numeroOrden,
                $monto,
                $usuarioId,
                'ING-PAGO-' . $id
            ]);
        }

        // =====================================================
        // 3. (Opcional pero recomendado) Actualizar la factura/orden a COBRADA
        // =====================================================
        if ($ordenId) {
            // Ajusta el nombre de la tabla y el campo según tu estructura
            $this->db->prepare("
                UPDATE ordenes_venta 
                SET estado = 'cobrado' 
                WHERE id = ?
            ")->execute([$ordenId]);
            
            // Si usas tabla de facturas separada:
            // $this->db->prepare("UPDATE facturas SET estado = 'cobrada' WHERE orden_id = ?")->execute([$ordenId]);
        }

        return [
            'conciliado' => true,
            'ingreso_generado' => true,
            'monto' => $monto
        ];
    }
}
