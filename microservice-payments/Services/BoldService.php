<?php

class BoldService {
    
    private array $cfg;
    
    public function __construct(){ 
        $this->cfg=(require __DIR__.'/../../../config/app.php')['bold']; 
    }

    public function createPayment(float $amount, string $description, array $meta=[]): array {

        if(!$this->cfg['enabled']) {
            return ['success'=>false,'message'=>'Bold deshabilitado'];
        }
        
        $pid='BOLD-'.strtoupper(bin2hex(random_bytes(8)));
        
        return [
            'success'=>true,
            'payment_id'=>$pid,
            'checkout_url'=>'https://checkout.bold.co/payment/'.$pid,
            'amount'=>$amount,
            'currency'=>'COP',
            'description'=>$description,
            'status'=>'pending',
            'mode'=>'sandbox',
            'meta'=>$meta
        ];
    }
    public function getPaymentStatus(string $paymentId): array {

        return [
            'success'=>true,
            'payment_id'=>$paymentId,
            'status'=>'approved',
            'mode'=>'sandbox'
        ];
    
    }
}
