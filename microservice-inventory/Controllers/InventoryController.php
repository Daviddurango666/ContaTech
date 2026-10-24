<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Models/ProductModel.php';

class InventoryController extends Controller {
    private ProductModel $products; 
    private CategoryModel $categories;

    public function __construct(){
        parent::__construct();
        $this->products=new ProductModel();
        $this->categories=new CategoryModel();
    }
    
    public function indexProducts(): void { 
        AuthMiddleware::handle('inventario','read'); 
        Response::success($this->products->allWithCategory()); 
    }
    
    public function showProduct(int $id): void { 
        AuthMiddleware::handle('inventario','read'); 
        $p=$this->products->findById($id); 
        if(!$p)Response::error('No encontrado.',404); 
        
        Response::success($p); 
    }
    
    public function storeProduct(): void {
        AuthMiddleware::handle('inventario','create'); 
        $d=$this->input();
        if(empty($d['codigo'])||empty($d['nombre'])) {
            Response::error('Código y nombre obligatorios.',422);
        }
        try{ 
            Response::success(['id'=>$this->products->create($d)],'Producto creado.',201); 
        } catch(PDOException $e){ 
            Response::error('Código duplicado.',409); 
        }
    }
    public function updateProduct(int $id): void {
        AuthMiddleware::handle('inventario','update'); 
        if(!$this->products->findById($id)){
            Response::error('No encontrado.',404);
        }
        $this->products->updateProduct($id,$this->input()); 
        Response::success(null,'Actualizado.');
    }
    public function deactivateProduct(int $id): void {
        AuthMiddleware::handle('inventario','delete'); 
        $p=$this->products->findById($id); 
        if(!$p) {
            Response::error('No encontrado.',404);
        }
        $this->products->updateProduct($id,array_merge($p,['activo'=>0])); 
        Response::success(null,'Desactivado.');
    }
    public function updateStock(int $id): void {
        AuthMiddleware::handle('inventario','stock'); 
        $d=$this->input();
        if(!$this->products->findById($id)){
            Response::error('No encontrado.',404);
        }
        $this->products->updateStock($id,(int)($d['cantidad']??0),$d['operacion']??'set');
        Response::success($this->products->findById($id),'Stock actualizado.');
    }
    public function lowStock(): void { 
        AuthMiddleware::handle('inventario','read'); 
        Response::success($this->products->lowStock()); 
    }
    
    public function indexCategories(): void { 
        AuthMiddleware::handle('inventario','read'); 
        Response::success($this->categories->findAll('nombre ASC')); 
    }
    
    public function storeCategory(): void {
        AuthMiddleware::handle('inventario','create'); 
        $d=$this->input();
        if(empty($d['nombre'])){
            Response::error('Nombre obligatorio.',422);
        }
        Response::success(['id'=>$this->categories->create($d)],'Categoría creada.',201);
    }
    public function updateCategory(int $id): void {
        AuthMiddleware::handle('inventario','update'); 
        if(!$this->categories->findById($id)){
            Response::error('No encontrada.',404);
        }
        $this->categories->updateCategory($id,$this->input()); 
        Response::success(null,'Actualizada.');
    }
    public function deactivateCategory(int $id): void {
        AuthMiddleware::handle('inventario','delete'); 
        $c=$this->categories->findById($id); 
        if(!$c) {
            Response::error('No encontrada.',404);
        }
        $this->categories->updateCategory($id,array_merge($c,['activo'=>0])); 
        Response::success(null,'Desactivada.');
    }
}
