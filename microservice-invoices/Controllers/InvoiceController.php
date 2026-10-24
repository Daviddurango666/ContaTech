<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Models/InvoiceModel.php';
require_once __DIR__ . '/../../microservice-orders/Models/OrderModel.php';
require_once __DIR__ . '/../Services/DianService.php';
class InvoiceController extends Controller {
    private InvoiceModel $invoices; private OrderModel $orders;
    public function __construct(){parent::__construct();$this->invoices=new InvoiceModel();$this->orders=new OrderModel();}
    public function index(): void { AuthMiddleware::handle('facturas','read'); Response::success($this->invoices->allWithRelations()); }
    public function show(int $id): void { AuthMiddleware::handle('facturas','read'); $f=$this->invoices->findWithDetails($id); if(!$f)Response::error('No encontrada.',404); Response::success($f); }
    public function generate(): void {
        $auth=AuthMiddleware::handle('facturas','create'); $ordenId=(int)($this->input()['orden_id']??0);
        if(!$ordenId) Response::error('orden_id requerido.',422);
        $orden=$this->orders->findWithDetails($ordenId);
        if(!$orden) Response::error('Orden no encontrada.',404);
        if(in_array($orden['estado'],['anulado','facturado','cobrado'])) Response::error('Orden no facturable.',400);
        $items=[];
        foreach($orden['detalle'] as $it) $items[]=['producto_id'=>$it['producto_id'],'descripcion'=>$it['producto_nombre'],'cantidad'=>$it['cantidad'],'precio_unitario'=>$it['precio_unitario'],'subtotal'=>$it['subtotal']];
        $fid=$this->invoices->createFromOrder(['numero'=>$this->invoices->nextNumber(),'orden_id'=>$ordenId,'cliente_id'=>$orden['cliente_id'],'usuario_id'=>$auth['id'],'fecha'=>date('Y-m-d'),'subtotal'=>$orden['subtotal'],'impuestos'=>$orden['impuestos'],'total'=>$orden['total'],'estado'=>'emitida'],$items);
        $this->orders->updateStatus($ordenId,'facturado');
        $this->invoices->markAccounting($fid);
        Response::success($this->invoices->findWithDetails($fid),'Factura generada y contabilizada.',201);
    }
    public function process(int $id): void {
        AuthMiddleware::handle('facturas','process'); if(!$this->invoices->findById($id))Response::error('No encontrada.',404);
        $this->invoices->markAccounting($id); Response::success($this->invoices->findWithDetails($id),'Contabilizada.');
    }
    public function sendDian(int $id): void {
        AuthMiddleware::handle('facturas','dian'); $f=$this->invoices->findWithDetails($id); if(!$f)Response::error('No encontrada.',404);
        $dian=new DianService(); $result=$dian->sendInvoice($f);
        $this->invoices->updateDian($id,$result['success']?'enviada_dian':'rechazada',$result['cufe']??null,json_encode($result,JSON_UNESCAPED_UNICODE));
        if($result['success']) Response::success($this->invoices->findWithDetails($id),'Enviada a DIAN.');
        Response::error('DIAN: '.($result['message']??'Error'),502,$result);
    }
}
