<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Models/OrderModel.php';
require_once __DIR__ . '/.././../microservice-invoices/Models/InvoiceModel.php';
class OrderController extends Controller {

    private OrderModel $orders;
    private InvoiceModel $invoices;
    
    public function __construct(){
        parent::__construct();
        $this->orders=new OrderModel();
        $this->invoices=new InvoiceModel();
    }
    
    public function index(): void { 
        AuthMiddleware::handle('pedidos','read'); 
        Response::success($this->orders->allWithRelations()); 
    }

    public function lastOrders(): void {
        AuthMiddleware::handle('pedidos','read'); 
        $o = $this->orders->lastOrders();

        Response::success($o); 
    }

    public function show(int $id): void { 
        AuthMiddleware::handle('pedidos','read'); 
        $o=$this->orders->findWithDetails($id); 
        if(!$o){
            Response::error('No encontrada.',404); 
        }
        Response::success($o); 
    }

    public function store(): void {
        $auth = AuthMiddleware::handle('pedidos', 'create');
        $d = $this->input();
        $items = $d['items'] ?? [];
        if (!$items) {
            Response::error('La orden debe tener productos.', 422);
        }

        $sub = 0;
        $norm = [];
        foreach ($items as $it) {
            $c = (int)($it['cantidad'] ?? 0);
            $p = (float)($it['precio_unitario'] ?? 0);
            if ($c <= 0 || empty($it['producto_id'])) continue;
            $line = $c * $p;
            $sub += $line;
            $norm[] = [
                'producto_id' => (int)$it['producto_id'],
                'cantidad' => $c,
                'precio_unitario' => $p,
                'subtotal' => $line,
            ];
        }
        if (!$norm) {
            Response::error('Items inválidos.', 422);
        }

        $imp = round($sub * 0.19, 2);
        $tot = $sub + $imp;
        $autoFactura = !empty($d['auto_factura']);
        $autoPago = !empty($d['auto_pago']);

        try {
            // 1) Orden
            $ordenId = $this->orders->createWithItems([
                'numero' => $this->orders->nextNumber(),
                'cliente_id' => $d['cliente_id'] ?? null,
                'usuario_id' => $auth['id'],
                'estado' => $autoFactura ? 'facturado' : 'pendiente',
                'subtotal' => $sub,
                'impuestos' => $imp,
                'total' => $tot,
                'observaciones' => $d['observaciones'] ?? null,
            ], $norm);

            $facturaId = null;
            $pagoId = null;

            // 2) Factura automática
            if ($autoFactura) {
                $orden = $this->orders->findWithDetails($ordenId);
                $facItems = [];
                foreach ($orden['detalle'] as $it) {
                    $facItems[] = [
                        'producto_id' => $it['producto_id'],
                        'descripcion' => $it['producto_nombre'] ?? 'Producto',
                        'cantidad' => $it['cantidad'],
                        'precio_unitario' => $it['precio_unitario'],
                        'subtotal' => $it['subtotal'],
                    ];
                }
                $facturaId = $this->invoices->createFromOrder([
                    'numero' => $this->invoices->nextNumber(),
                    'orden_id' => $ordenId,
                    'cliente_id' => $orden['cliente_id'],
                    'usuario_id' => $auth['id'],
                    'fecha' => date('Y-m-d'),
                    'subtotal' => $orden['subtotal'],
                    'impuestos' => $orden['impuestos'],
                    'total' => $orden['total'],
                    'estado' => 'emitida',
                ], $facItems);

                // Contabilizar
                $this->invoices->markAccounting($facturaId);
            }

            // 3) Pago automático
            if ($autoPago && $facturaId) {
                $metodo = (int)($d['metodo_pago_id'] ?? 1);
                // Ajusta a tu PaymentModel / INSERT pagos
                $this->db->prepare("
                    INSERT INTO pagos
                    (factura_id, orden_id, metodo_pago_id, monto, monto_factura, estado, conciliado, usuario_id)
                    VALUES (?, ?, ?, ?, ?, 'pendiente', 0, ?)
                ")->execute([
                    $facturaId, $ordenId, $metodo, $tot, $tot, $auth['id']
                ]);
                $pagoId = (int)$this->db->lastInsertId();

                // Orden facturada, no cobrada
                $this->orders->updateStatus($ordenId, 'facturado');
            }

            // Egreso por costo de mercancía (opcional)
            $costo = 0;
            foreach ($orden['detalle'] as $it) {
                // Si traes costo en el detalle, o lo consultas del producto
                $st = $this->db->prepare("SELECT costo FROM productos WHERE id = ?");
                $st->execute([$it['producto_id']]);
                $row = $st->fetch();
                $costo += ((float)($row['costo'] ?? 0)) * (int)$it['cantidad'];
            }

            if ($costo > 0) {
                $this->db->prepare("
                    INSERT INTO movimientos
                    (tipo, descripcion, monto, categoria, fecha, usuario_id, referencia)
                    VALUES
                    ('egreso', ?, ?, 'costo_ventas', ?, ?, ?)
                ")->execute([
                    'Costo venta orden ' . ($orden['numero'] ?? $ordenId),
                    $costo,
                    date('Y-m-d'),
                    $auth['id'],
                    'COSTO-OV-' . $ordenId,
                ]);
            }

            Response::success([
                'orden' => $this->orders->findWithDetails($ordenId),
                'factura_id' => $facturaId,
                'pago_id' => $pagoId,
                'numero' => $this->orders->findById($ordenId)['numero'] ?? null,
            ], 'Orden, factura y pago registrados.', 201);

        } catch (Throwable $e) {
            Response::error('Error: ' . $e->getMessage(), 500);
        }
    }
    public function update(int $id): void {
        AuthMiddleware::handle('pedidos','update'); 
        $o=$this->orders->findById($id);

        if(!$o){
            Response::error('No encontrada.',404);
        }
        if(in_array($o['estado'],['anulado','cobrado'])){
            Response::error('No modificable.',400);
        } 
        $d=$this->input();

        if(isset($d['estado'])) {
            $this->orders->updateStatus($id,$d['estado']);
        }
        if(isset($d['observaciones'])) {
            $this->db->prepare("
                                UPDATE ordenes_venta 
                                SET 
                                observaciones=? 
                                WHERE 
                                id=?
            ")->execute([$d['observaciones'],$id]);

        }
        Response::success($this->orders->findWithDetails($id),'Actualizada.');
    }
    public function cancel(int $id): void {
        AuthMiddleware::handle('pedidos','cancel'); 
        $o=$this->orders->findWithDetails($id);
        if(!$o){
            Response::error('No encontrada.',404);
        }
        
        if($o['estado']==='anulado'){
            Response::error('Ya anulada.',400);
        }
        
        if($o['estado']==='cobrado'){
            Response::error('No se puede anular cobrada.',400);
        }

        $this->db->beginTransaction();
        try{
            $st=$this->db->prepare("
                                    UPDATE 
                                    productos 
                                    SET 
                                    stock=stock+? 
                                    WHERE 
                                    id=?
            ");
            
            foreach($o['detalle'] as $it) {
                $st->execute([
                    $it['cantidad'],
                    $it['producto_id']
                ]);
            }
            $this->orders->updateStatus($id,'anulado'); 
            $this->db->commit();

        }catch(Exception $e){ 
            $this->db->rollBack(); 
            Response::error($e->getMessage(),500); 
        }
        
        Response::success(null,'Orden anulada.');
    }
}
