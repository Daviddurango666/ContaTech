<?php

require_once __DIR__ . '/../../core/Controller.php';

class ReportController extends Controller {
    
    public function balance(): void {

        AuthMiddleware::handle('informes','view');

        $act=(float)$this->db->query("
                                        SELECT COALESCE(SUM(monto),0) t 
                                        FROM movimientos 
                                        WHERE 
                                        tipo='ingreso'
                                    ")->fetch()['t'];

        $pas=(float)$this->db->query("
                                        SELECT COALESCE(SUM(monto),0) t 
                                        FROM movimientos 
                                        WHERE 
                                        tipo='egreso'
                                    ")->fetch()['t'];

        $inv=(float)$this->db->query("
                                        SELECT COALESCE(SUM(stock*costo),0) t 
                                        FROM productos 
                                        WHERE 
                                        activo=1
                                    ")->fetch()['t'];

        Response::success([
            'activos'=>[
                'caja_bancos'=>$act-$pas,
                'inventario'=>$inv,
                'total'=>($act-$pas)+$inv
            ],
            'pasivos'=>[
                'total'=>0
            ],
            'patrimonio'=>($act-$pas)+$inv,
            'fecha'=>date('Y-m-d')
        ]);
    }
    public function estadoResultados(): void {

        AuthMiddleware::handle('informes','view');
        
        $ventas=(float)$this->db->query("
                                            SELECT COALESCE(SUM(total),0) t 
                                            FROM facturas 
                                            WHERE 
                                            estado 
                                            NOT IN 
                                            ('anulada','borrador')
                                        ")->fetch()['t'];
        
        $otros=(float)$this->db->query("
                                            SELECT COALESCE(SUM(monto),0) t 
                                            FROM movimientos 
                                            WHERE 
                                            tipo='ingreso' 
                                            AND IFNULL
                                            (categoria,'')!='ventas'
                                        ")->fetch()['t'];
        
        $gastos=(float)$this->db->query("
                                            SELECT COALESCE(SUM(monto),0) t 
                                            FROM movimientos 
                                            WHERE 
                                            tipo='egreso'
                                        ")->fetch()['t'];
        
        $costo=(float)$this->db->query("
                                        SELECT COALESCE(SUM(fd.cantidad*p.costo),0) t 
                                        FROM facturas_detalle fd 
                                        JOIN productos p ON p.id=fd.producto_id 
                                        JOIN facturas f ON f.id=fd.factura_id 
                                        WHERE 
                                        f.estado 
                                        NOT IN 
                                        ('anulada','borrador')
                                    ")->fetch()['t'];
        
        $ub=$ventas-$costo; 
        
        Response::success([
            'ingresos'=>[
                'ventas'=>$ventas,
                'otros'=>$otros,
                'total'=>$ventas+$otros
            ],
            'costo_ventas'=>$costo,
            'utilidad_bruta'=>$ub,
            'gastos_operativos'=>$gastos,
            'utilidad_neta'=>$ub+$otros-$gastos,
            'fecha'=>date('Y-m-d')
        ]);
    }
    public function flujoCaja(): void {
        AuthMiddleware::handle('informes','view'); 
        $d=$this->input();
        $desde=$d['desde']??date('Y-m-01'); 
        $hasta=$d['hasta']??date('Y-m-d');

        $s=$this->db->prepare("
                                SELECT 
                                fecha,
                                tipo,
                                SUM(monto) total 
                                FROM movimientos 
                                WHERE 
                                fecha BETWEEN ? AND ? 
                                GROUP BY 
                                fecha,
                                tipo 
                                ORDER BY 
                                fecha
                            ");
        $s->execute([
            $desde,
            $hasta
        ]); 
        
        $rows=$s->fetchAll(); 
        $por=[];
        
        foreach($rows as $r){ 
            $f=$r['fecha']; 
            if(!isset($por[$f])) {
                $por[$f]=[
                    'fecha'=>$f,
                    'ingresos'=>0,
                    'egresos'=>0
                ]; 
            }
                
            $por[$f][$r['tipo']==='ingreso'?'ingresos':'egresos']=(float)$r['total']; 
        }

        Response::success([
            'desde'=>$desde,
            'hasta'=>$hasta,
            'detalle'=>array_values($por),
            'totales'=>[
                'ingresos'=>array_sum(
                    array_column(array_values($por),
                    'ingresos')
                ),
                'egresos'=>array_sum(
                    array_column(array_values($por),
                    'egresos')
                )
            ]
        ]);
    }
    public function impuestos(): void {
        
        AuthMiddleware::handle('informes','view');
        
        $iva=(float)$this->db->query("
                                        SELECT COALESCE(SUM(impuestos),0) t 
                                        FROM facturas
                                        WHERE 
                                        estado NOT IN ('anulada','borrador')
                                    ")->fetch()['t'];
        
        Response::success([
            'iva_generado'=>$iva,
            'periodo'=>date('Y-m'),
            'nota'=>'Reporte simplificado.'
        ]);
    }
    
    public function dashboard(): void {
        
        AuthMiddleware::handle('dashboard','view');
        
        $ing=(float)$this->db->query("
                                        SELECT COALESCE(SUM(monto),0) t 
                                        FROM movimientos 
                                        WHERE 
                                        tipo='ingreso'
                                    ")->fetch()['t'];
        
        $egr=(float)$this->db->query("
                                        SELECT COALESCE(SUM(monto),0) t 
                                        FROM movimientos 
                                        WHERE 
                                        tipo='egreso'
                                    ")->fetch()['t'];
        
        Response::success([
            'ingresos'=>$ing,
            'egresos'=>$egr,
            'saldo'=>$ing-$egr,

            'productos'=>(int)$this->db->query("
                                                SELECT COUNT(*) c 
                                                FROM productos 
                                                WHERE 
                                                activo=1
                                            ")->fetch()['c'],

            'alertas_stock'=>(int)$this->db->query("
                                                SELECT COUNT(*) c 
                                                FROM productos 
                                                WHERE 
                                                activo=1 
                                                AND 
                                                stock<=stock_minimo
                                            ")->fetch()['c'],

            'facturas'=>(int)$this->db->query("
                                                SELECT COUNT(*) c 
                                                FROM 
                                                facturas
                                            ")->fetch()['c'],

            'ordenes_pendientes'=>(int)$this->db->query("
                                                SELECT COUNT(*) c 
                                                FROM ordenes_venta 
                                                WHERE 
                                                estado='pendiente'
                                            ")->fetch()['c'],
        ]);
    }
}
