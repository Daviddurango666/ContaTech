<?php


require_once __DIR__ . '/../../core/Controller.php';

class ReportController extends Controller {

    public function balance(): void {
        AuthMiddleware::handle('informes', 'view');

        $act = (float)$this->db->query("
            SELECT COALESCE(SUM(monto),0) t
            FROM movimientos
            WHERE tipo = 'ingreso'
        ")->fetch()['t'];

        $pas = (float)$this->db->query("
            SELECT COALESCE(SUM(monto),0) t
            FROM movimientos
            WHERE tipo = 'egreso'
        ")->fetch()['t'];

        $inv = (float)$this->db->query("
            SELECT COALESCE(SUM(stock * costo),0) t
            FROM productos
            WHERE activo = 1
        ")->fetch()['t'];

        Response::success([
            'activos' => [
                'caja_bancos' => $act - $pas,
                'inventario'  => $inv,
                'total'       => ($act - $pas) + $inv,
            ],
            'pasivos'    => ['total' => 0],
            'patrimonio' => ($act - $pas) + $inv,
            'fecha'      => date('Y-m-d'),
        ]);
    }

    public function estadoResultados(): void {
        AuthMiddleware::handle('informes', 'view');

        $ventas = (float)$this->db->query("
            SELECT COALESCE(SUM(total),0) t
            FROM facturas
            WHERE estado NOT IN ('anulada','borrador')
        ")->fetch()['t'];

        $otros = (float)$this->db->query("
            SELECT COALESCE(SUM(monto),0) t
            FROM movimientos
            WHERE tipo = 'ingreso'
              AND IFNULL(categoria,'') != 'ventas'
        ")->fetch()['t'];

        $gastos = (float)$this->db->query("
            SELECT COALESCE(SUM(monto),0) t
            FROM movimientos
            WHERE tipo = 'egreso'
        ")->fetch()['t'];

        $costo = (float)$this->db->query("
            SELECT COALESCE(SUM(fd.cantidad * p.costo),0) t
            FROM facturas_detalle fd
            JOIN productos p ON p.id = fd.producto_id
            JOIN facturas f ON f.id = fd.factura_id
            WHERE f.estado NOT IN ('anulada','borrador')
        ")->fetch()['t'];

        $ub = $ventas - $costo;

        Response::success([
            'ingresos' => [
                'ventas' => $ventas,
                'otros'  => $otros,
                'total'  => $ventas + $otros,
            ],
            'costo_ventas'      => $costo,
            'utilidad_bruta'    => $ub,
            'gastos_operativos' => $gastos,
            'utilidad_neta'     => $ub + $otros - $gastos,
            'fecha'             => date('Y-m-d'),
        ]);
    }

    public function flujoCaja(): void {
        AuthMiddleware::handle('informes', 'view');
        $d     = $this->input();
        $desde = $d['desde'] ?? date('Y-m-01');
        $hasta = $d['hasta'] ?? date('Y-m-d');

        $s = $this->db->prepare("
            SELECT fecha, tipo, SUM(monto) total
            FROM movimientos
            WHERE fecha BETWEEN ? AND ?
            GROUP BY fecha, tipo
            ORDER BY fecha
        ");
        $s->execute([$desde, $hasta]);
        $rows = $s->fetchAll();
        $por  = [];

        foreach ($rows as $r) {
            $f = $r['fecha'];
            if (!isset($por[$f])) {
                $por[$f] = ['fecha' => $f, 'ingresos' => 0, 'egresos' => 0];
            }
            $por[$f][$r['tipo'] === 'ingreso' ? 'ingresos' : 'egresos'] = (float)$r['total'];
        }

        $detalle = array_values($por);

        Response::success([
            'desde'   => $desde,
            'hasta'   => $hasta,
            'detalle' => $detalle,
            'totales' => [
                'ingresos' => array_sum(array_column($detalle, 'ingresos')),
                'egresos'  => array_sum(array_column($detalle, 'egresos')),
            ],
        ]);
    }

    public function impuestos(): void {
        AuthMiddleware::handle('informes', 'view');

        $iva = (float)$this->db->query("
            SELECT COALESCE(SUM(impuestos),0) t
            FROM facturas
            WHERE estado NOT IN ('anulada','borrador')
        ")->fetch()['t'];

        Response::success([
            'iva_generado' => $iva,
            'periodo'      => date('Y-m'),
            'nota'         => 'Reporte simplificado.',
        ]);
    }

    /**
     * Dashboard unificado.
     * Causa del error anterior: OrderModel::lastOrders() NO existía.
     * Aquí se consulta con SQL directo (sin depender del modelo).
     */
    public function dashboard(): void {
        AuthMiddleware::handle('dashboard', 'view');

        try {
            $ing = (float)$this->db->query("
                SELECT COALESCE(SUM(monto),0) t
                FROM movimientos
                WHERE tipo = 'ingreso'
            ")->fetch()['t'];

            $egr = (float)$this->db->query("
                SELECT COALESCE(SUM(monto),0) t
                FROM movimientos
                WHERE tipo = 'egreso'
            ")->fetch()['t'];

            $productos = (int)$this->db->query("
                SELECT COUNT(*) c FROM productos WHERE activo = 1
            ")->fetch()['c'];

            $alertasStock = (int)$this->db->query("
                SELECT COUNT(*) c
                FROM productos
                WHERE activo = 1 AND stock <= stock_minimo
            ")->fetch()['c'];

            $facturas = (int)$this->db->query("
                SELECT COUNT(*) c FROM facturas
            ")->fetch()['c'];

            $facturasPend = (int)$this->db->query("
                SELECT COUNT(*) c
                FROM facturas
                WHERE estado IN ('borrador','emitida')
            ")->fetch()['c'];

            $pend = (int)$this->db->query("
                SELECT COUNT(*) c FROM ordenes_venta WHERE estado = 'pendiente'
            ")->fetch()['c'];

            $factOV = (int)$this->db->query("
                SELECT COUNT(*) c FROM ordenes_venta WHERE estado = 'facturado'
            ")->fetch()['c'];

            $cobr = (int)$this->db->query("
                SELECT COUNT(*) c FROM ordenes_venta WHERE estado = 'cobrado'
            ")->fetch()['c'];

            $anul = (int)$this->db->query("
                SELECT COUNT(*) c FROM ordenes_venta WHERE estado = 'anulado'
            ")->fetch()['c'];

            $ordenesHoy = (int)$this->db->query("
                SELECT COUNT(*) c
                FROM ordenes_venta
                WHERE DATE(created_at) = CURDATE()
            ")->fetch()['c'];

            $comprasSolic = (int)$this->db->query("
                SELECT COUNT(*) c FROM ordenes_compra WHERE estado = 'solicitado'
            ")->fetch()['c'];

            $comprasPorEnt = (int)$this->db->query("
                SELECT COUNT(*) c FROM ordenes_compra WHERE estado = 'facturado_proveedor'
            ")->fetch()['c'];

            $proveedores = (int)$this->db->query("
                SELECT COUNT(*) c FROM proveedores WHERE activo = 1
            ")->fetch()['c'];

            $usuariosActivos = (int)$this->db->query("
                SELECT COUNT(*) c FROM usuarios WHERE activo = 1
            ")->fetch()['c'];

            // Reemplazo de OrderModel::lastOrders(5)
            $ultimasOrdenes = $this->db->query("
                SELECT
                    ov.id,
                    ov.numero,
                    ov.estado,
                    ov.total,
                    COALESCE(c.nombre, '—') AS cliente_nombre
                FROM ordenes_venta ov
                LEFT JOIN clientes c ON c.id = ov.cliente_id
                ORDER BY ov.id DESC
                LIMIT 5
            ")->fetchAll();

            $productosBajoStock = $this->db->query("
                SELECT id, codigo, nombre, stock, stock_minimo, unidad
                FROM productos
                WHERE activo = 1 AND stock <= stock_minimo
                ORDER BY stock ASC
                LIMIT 10
            ")->fetchAll();

            $ultimasCompras = $this->db->query("
                SELECT
                    oc.id,
                    oc.numero,
                    oc.estado,
                    oc.total,
                    COALESCE(p.razon_social, '—') AS proveedor_nombre
                FROM ordenes_compra oc
                LEFT JOIN proveedores p ON p.id = oc.proveedor_id
                ORDER BY oc.id DESC
                LIMIT 5
            ")->fetchAll();

            Response::success([
                'ingresos'             => $ing,
                'egresos'              => $egr,
                'saldo'                => $ing - $egr,
                'productos'            => $productos,
                'alertas_stock'        => $alertasStock,
                'facturas'             => $facturas,
                'facturas_pendientes'  => $facturasPend,
                'ordenes_pendientes'   => $pend,
                'ordenes_hoy'          => $ordenesHoy,
                'ordenes_por_estado'   => [
                    'pendiente' => $pend,
                    'facturado' => $factOV,
                    'cobrado'   => $cobr,
                    'anulado'   => $anul,
                ],
                'compras_solicitadas'  => $comprasSolic,
                'compras_por_entregar' => $comprasPorEnt,
                'proveedores'          => $proveedores,
                'usuarios_activos'     => $usuariosActivos,
                'ultimas_ordenes'      => $ultimasOrdenes,
                'productos_bajo_stock' => $productosBajoStock,
                'ultimas_compras'      => $ultimasCompras,
            ]);
        } catch (Throwable $e) {
            Response::error('Error en dashboard: ' . $e->getMessage(), 500);
        }
    }
}
