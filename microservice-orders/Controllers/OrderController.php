<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Models/OrderModel.php';
class OrderController extends Controller {

    private OrderModel $orders;
    
    public function __construct(){
        parent::__construct();
        $this->orders=new OrderModel();
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
        $auth=AuthMiddleware::handle('pedidos','create'); 
        $d=$this->input(); 
        $items=$d['items']??[];

        if(!$items) {
            Response::error('La orden debe tener productos.',422);
        }
        
        $sub=0; 
        $norm=[];

        foreach($items as $it){
            $c=(int)($it['cantidad']??0); 
            $p=(float)($it['precio_unitario']??0);
            if($c<=0||empty($it['producto_id'])){
                continue;

            } 
            $line=$c*$p; 
            $sub+=$line;
            $norm[]=[
                'producto_id'=>(int)$it['producto_id'],
                'cantidad'=>$c,
                'precio_unitario'=>$p,
                'subtotal'=>$line
            ];
        }
        if(!$norm) {
            Response::error('Items inválidos.',422);
        }
        $imp=round($sub*0.19,2); 
        $tot=$sub+$imp;
        
        $id=$this->orders->createWithItems([
            'numero'=>$this->orders->nextNumber(),
            'cliente_id'=>$d['cliente_id']??null,
            'usuario_id'=>$auth['id'],
            'subtotal'=>$sub,
            'impuestos'=>$imp,
            'total'=>$tot,
            'observaciones'=>$d['observaciones']??null
        ],$norm);

        Response::success($this->orders->findWithDetails($id),'Orden creada.',201);
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
