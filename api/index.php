<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Authorization, Content-Type');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { 
    http_response_code(204); 
    exit; 
}

require_once __DIR__ . '/../core/Response.php';
require_once __DIR__ . '/../core/AuthMiddleware.php';
require_once __DIR__ . '/../microservice-auth/Controllers/AuthController.php';
require_once __DIR__ . '/../microservice-users/Controllers/UserController.php';
require_once __DIR__ . '/../microservice-providers/Controllers/ProviderController.php';
require_once __DIR__ . '/../microservice-inventory/Controllers/InventoryController.php';
require_once __DIR__ . '/../microservice-orders/Controllers/OrderController.php';
require_once __DIR__ . '/../microservice-invoices/Controllers/InvoiceController.php';
require_once __DIR__ . '/../microservice-payments/Controllers/PaymentController.php';
require_once __DIR__ . '/../microservice-purchases/Controllers/PurchaseController.php';
require_once __DIR__ . '/../microservice-cashFlow/Controllers/CashFlowController.php';
require_once __DIR__ . '/../microservice-reports/Controllers/ReportController.php';

$path = $_GET['path'] ?? '';

if ($path === '' && !empty($_SERVER['PATH_INFO'])){
    $path = ltrim($_SERVER['PATH_INFO'], '/');
}

$path = trim($path, '/');
$parts = $path === '' ? [] : explode('/', $path);
$method = strtoupper($_SERVER['REQUEST_METHOD']);

try { 
    route($parts, $method); 
} catch (Throwable $e) { 
    Response::error('Error interno: ' . $e->getMessage(), 500); 
}

function route(array $parts, string $method): void {

    $res = $parts[0] ?? '';
    
    $id = isset($parts[1]) && ctype_digit($parts[1]) ? (int)$parts[1] : null;

    $action = $id !== null ? ($parts[2] ?? null) : ($parts[1] ?? null);

    switch ($res) {
        case 'auth':
            $c = new AuthController();
            if ($method==='POST' && ($parts[1]??'')==='login'){
                $c->login();
            }
            if ($method==='POST' && ($parts[1]??'')==='logout'){ 
                $c->logout();
            }
            if ($method==='GET' && ($parts[1]??'')==='me'){
                $c->me();
            } 
            if ($method==='POST' && ($parts[1]??'')==='forgot-password'){
                $c->forgotPassword();
            } 
            if ($method==='POST' && ($parts[1]??'')==='reset-password'){
                $c->resetPassword();
            } 
            if ($method==='POST' && ($parts[1]??'')==='change-password'){
                $c->changePassword();
            } 
            break;
        case 'usuarios':
            $c = new UserController();
            if ($method==='GET' && $id===null){
                $c->index();
            } 
            if ($method==='GET' && $id!==null){
                $c->show($id);
            }
            if ($method==='POST' && $id===null){
                $c->store();
            }
            if ($method==='POST' && $id!==null && $action==='delete'){
                $c->destroy($id);
            } 
            if (($method==='PUT'||$method==='POST') && $id!==null && $action===null){
                $c->update($id);
            }
            if ($method==='DELETE' && $id!==null) {
                $c->destroy($id);
            }
            break;

        case 'proveedores':
            $c = new ProviderController();
            if ($method==='GET' && $id===null){
                $c->index();
            } 
            if ($method==='GET' && $id!==null){
                $c->show($id);
            } 
            if ($method==='POST' && $id===null){
                $c->store();
            }
            if ($method==='POST' && $id!==null && $action==='delete') {
                $c->destroy($id);
            }
            if (($method==='PUT'||$method==='POST') && $id!==null && $action===null){
                $c->update($id);
            }
            if ($method==='DELETE' && $id!==null){
                $c->destroy($id);
            }
            break;

        case 'productos':
            $c = new InventoryController();
            if ($method==='GET' && ($parts[1]??'')==='alertas') {
                $c->lowStock();
            }
            if ($method==='GET' && $id===null) {
                $c->indexProducts();
            }
            if ($method==='GET' && $id!==null) {
                $c->showProduct($id);
            } 
            if ($method==='POST' && $id===null) {
                $c->storeProduct();
            } 
            if ($method==='POST' && $id!==null && $action==='stock') {
                $c->updateStock($id);
            }
            if ($method==='POST' && $id!==null && $action==='desactivar') {
                $c->deactivateProduct($id);
            }
            if (($method==='PUT'||$method==='POST') && $id!==null && $action===null) {
                $c->updateProduct($id);    
            }
            break;

        case 'categorias':
            $c = new InventoryController();
            if ($method==='GET') {
                $c->indexCategories();
            }
            if ($method==='POST' && $id===null)  {
                $c->storeCategory();
            }
            if ($method==='POST' && $id!==null && $action==='desactivar') {
                $c->deactivateCategory($id);
            }
            if (($method==='PUT'||$method==='POST') && $id!==null) {
                $c->updateCategory($id);
            }
            break;
        case 'pedidos':
            $c = new OrderController();
            if ($method==='GET' && $id===null) {
                $c->index();
            }
            if ($method==='GET' && $id!==null) {
                $c->show($id);
            }
            if ($method==='POST' && $id===null) {
                $c->store();
            }
            if ($method==='POST' && $id!==null && $action==='cancelar') {
                $c->cancel($id);
            }
            if (($method==='PUT'||$method==='POST') && $id!==null && $action===null) {
                $c->update($id);
            }
            break;
        case 'facturas':
            $c = new InvoiceController();
            if ($method==='GET' && $id===null) {
                $c->index();
            }
            if ($method==='GET' && $id!==null) {
                $c->show($id);
            }
            if ($method==='POST' && ($parts[1]??'')==='generar'){
                $c->generate();
            } 
            if ($method==='POST' && $id!==null && $action==='procesar') {
                $c->process($id);
            }
            if ($method==='POST' && $id!==null && $action==='dian') {
                $c->sendDian($id);
            }
            break;
        case 'pagos':
            $c = new PaymentController();
            if ($method==='GET' && ($parts[1]??'')==='metodos') {
                $c->methods();
            }
            if ($method==='GET' && $id===null) {
                $c->index();
            }
            if ($method==='POST' && ($parts[1]??'')==='bold') {
                $c->boldCheckout();
            }
            if ($method==='POST' && $id===null) {
                $c->store();
            }
            if ($method==='POST' && $id!==null && $action==='bold-confirm') {
                $c->boldConfirm($id);
            }
            if ($method==='POST' && $id!==null && $action==='conciliar') {
                $c->conciliate($id);
            }
            break;
        case 'compras':
            $c = new PurchaseController();
            if ($method==='GET' && $id===null) {
                $c->index();
            }
            if ($method==='GET' && $id!==null) {
                $c->show($id);
            }
            if ($method==='POST' && $id===null) {
                $c->store();
            }
            if ($method==='POST' && $id!==null && $action==='cancelar') {
                $c->cancel($id);
            }
            if (($method==='PUT'||$method==='POST') && $id!==null && $action===null) {
                $c->update($id);
            }
            break;
        case 'movimientos':
            $c = new CashFlowController();
            if ($method==='GET') {
                $c->index();
            }
            if ($method==='POST') {
                $c->store();
            }
            break;
        case 'informes':
            $c = new ReportController();
            $sub = $parts[1] ?? '';
            if ($sub==='balance'){ 
                $c->balance();
            }
            if ($sub==='estado-resultados') {
                $c->estadoResultados();
            }
            if ($sub==='flujo-caja') {
                $c->flujoCaja();
            }
            if ($sub==='impuestos'){
                $c->impuestos();
            } 
            if ($sub==='dashboard') {
                $c->dashboard();
            }
            break;
        default:
            Response::error('Endpoint no encontrado: '.implode('/',$parts), 404);
    }
    Response::error('Método o ruta no soportada.', 405);
}
