<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Models/ProviderModel.php';
class ProviderController extends Controller {
    
    private ProviderModel $model;

    public function __construct(){
        parent::__construct();
        $this->model=new ProviderModel();
    }
    
    public function index(): void { 
        AuthMiddleware::handle('proveedores','read'); 
        Response::success($this->model->findAll('nit ASC')); 
    }
    
    public function show(int $id): void { 
        AuthMiddleware::handle('proveedores','read'); 
        $r=$this->model->findById($id); 
        
        if(!$r){
            Response::error('No encontrado.',404); 
        } else {
            Response::success($r);
        }
    }
    
    public function store(): void {

        AuthMiddleware::handle('proveedores','create'); 

        $d=$this->input();
        if(empty($d['nit'])||empty($d['razon_social'])) {
            Response::error('NIT y razón social obligatorios.',422);
        }
        
        try{ 
            Response::success([
                'id'=>$this->model->create($d)],
                'Proveedor registrado.',201); 

        } catch(PDOException $e){ 
            Response::error('NIT duplicado.',409); 
        }
    }
    
    public function update(int $id): void {

        AuthMiddleware::handle('proveedores','update');
        
        if(!$this->model->findById($id)) {
            Response::error('No encontrado.',404);
        }

        $this->model->updateProvider($id,$this->input()); 
        
        Response::success(null,'Actualizado.');
    }

    
    public function destroy(int $id): void {

        AuthMiddleware::handle('proveedores','delete');
        
        $p=$this->model->findById($id); 
        if(!$p){
            Response::error('No encontrado.',404);
        }

        $this->model->updateProvider($id,array_merge($p,['activo'=>0])); 
        Response::success(null,'Desactivado.');
    }
}
