<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Models/PaymentModel.php';
require_once __DIR__ . '/../Services/BoldService.php';
class PaymentController extends Controller {
    private PaymentModel $payments;
    public function __construct(){parent::__construct();$this->payments=new PaymentModel();}
    public function index(): void { AuthMiddleware::handle('pagos','read'); Response::success($this->payments->allWithRelations()); }
    public function methods(): void { AuthMiddleware::handle('pagos','read'); Response::success($this->payments->methods()); }
    public function store(): void {
        $auth=AuthMiddleware::handle('pagos','create'); $d=$this->input();
        if(empty($d['metodo_pago_id'])||!isset($d['monto'])) Response::error('metodo_pago_id y monto obligatorios.',422);
        $d['usuario_id']=$auth['id']; $d['estado']=$d['estado']??'aprobado';
        Response::success(['id'=>$this->payments->create($d)],'Pago registrado.',201);
    }
    public function boldCheckout(): void {
        $auth=AuthMiddleware::handle('pagos','pasarela'); $d=$this->input(); $amount=(float)($d['monto']??0);
        if($amount<=0) Response::error('Monto inválido.',422);
        $bold=new BoldService(); $result=$bold->createPayment($amount,$d['descripcion']??'Pago ContaTech',['factura_id'=>$d['factura_id']??null]);
        if(!$result['success']) Response::error($result['message']??'Error Bold',502);
        $pagoId=$this->payments->create(['factura_id'=>$d['factura_id']??null,'orden_id'=>$d['orden_id']??null,'metodo_pago_id'=>4,'monto'=>$amount,'monto_factura'=>$d['monto_factura']??$amount,'referencia'=>$result['payment_id'],'bold_payment_id'=>$result['payment_id'],'estado'=>'pendiente','usuario_id'=>$auth['id']]);
        Response::success(['pago_id'=>$pagoId,'bold'=>$result],'Checkout Bold creado.',201);
    }
    public function boldConfirm(int $pagoId): void {
        AuthMiddleware::handle('pagos','pasarela'); $p=$this->payments->findById($pagoId);
        if(!$p||!$p['bold_payment_id']) Response::error('Pago Bold no encontrado.',404);
        $status=(new BoldService())->getPaymentStatus($p['bold_payment_id']);
        if(($status['status']??'')==='approved') $this->db->prepare("UPDATE pagos SET estado='aprobado' WHERE id=?")->execute([$pagoId]);
        Response::success(['pago'=>$this->payments->findById($pagoId),'bold'=>$status]);
    }
    public function conciliate(int $id): void {
        AuthMiddleware::handle('pagos','conciliar');
        try{ $r=$this->payments->conciliate($id); if($r['conciliado']) Response::success($r,'Conciliado.'); Response::error('Montos no coinciden. Dif: '.$r['diferencia'],422,$r); }
        catch(Exception $e){ Response::error($e->getMessage(),404); }
    }
}
