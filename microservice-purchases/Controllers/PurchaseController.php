<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Models/PurchaseModel.php';
class PurchaseController extends Controller {

    
    private PurchaseModel $model;
    
    public function __construct(){
        parent::__construct();
        $this->model=new PurchaseModel();
    }
    
    public function index(): void { 
        AuthMiddleware::handle('compras','read'); 
        Response::success($this->model->allWithRelations()); 
    }
    
    public function show(int $id): void { 
        AuthMiddleware::handle('compras','read'); 
        $o=$this->model->findWithDetails($id); 
        
        if(!$o){
            Response::error('No encontrada.',404);
        } 
        Response::success($o); 
    
    }
    
    public function store(): void {

        $auth=AuthMiddleware::handle('compras','create'); 
        $d=$this->input();
        
        if(empty($d['proveedor_id'])||empty($d['items'])) {
            Response::error('proveedor_id e items obligatorios.',422);
        }
        
        $sub=0; 
        $norm=[];
        
        foreach($d['items'] as $it){

            $c=(int)($it['cantidad']??0); 
            $cost=(float)($it['costo_unitario']??0);
            
            if($c<=0||empty($it['producto_id'])){
                continue;
            } 
            
            $line=$c*$cost; 
            $sub+=$line;
            
            $norm[]=[
                'producto_id'=>(int)$it['producto_id'],
                'cantidad'=>$c,
                'costo_unitario'=>$cost,
                'subtotal'=>$line
            ];
        }

        if(!$norm) {
            Response::error('Items inválidos.',422);
        }
        $id=$this->model->createWithItems([
            'numero'=>$this->model->nextNumber(),
            'proveedor_id'=>(int)$d['proveedor_id'],
            'usuario_id'=>$auth['id'],
            'subtotal'=>$sub,
            'total'=>$sub,
            'observaciones'=>$d['observaciones']??null],
            $norm
        );
        
        Response::success($this->model->findWithDetails($id),'OC creada.',201);
    }
    
    public function update(int $id): void {

        AuthMiddleware::handle('compras','update'); 
        
        if(!$this->model->findById($id)){
            Response::error('No encontrada.',404);
        }

        $d=$this->input(); 
        
        if(isset($d['estado'])) {
            $this->model->updateStatus($id,$d['estado']);
        }
        Response::success($this->model->findWithDetails($id),'Actualizada.');
    }
    
    public function cancel(int $id): void {
        AuthMiddleware::handle('compras','cancel'); 
        $o=$this->model->findById($id);
        
        if(!$o){
            Response::error('No encontrada.',404);
        }
        if($o['estado']==='entregado') {
            Response::error('No anular entregada.',400);
        }
        
        $this->model->updateStatus($id,'anulado'); 
        
        Response::success(null,'Anulada.');
    }
}
