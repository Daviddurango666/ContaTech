<?php
class DianService {
    private array $cfg;
    public function __construct(){ 
        $this->cfg=(require __DIR__.'/../../config/app.php')['dian']; 
    }
    
    public function sendInvoice(array $factura): array {
        if(!$this->cfg['enabled']){
            return [
                'success'=>false,
                'message'=>'DIAN deshabilitada'
            ];
        }
        $cufe=$this->generateCufe($factura);

        if($this->cfg['test_mode']){
            return [
                'success'=>true,
                'cufe'=>$cufe,
                'message'=>'Aceptada en ambiente pruebas DIAN (simulado).',
                'mode'=>'test',
                'nit'=>$this->cfg['nit']
            ];
        }
        return [
            'success'=>true,
            'cufe'=>$cufe,
            'message'=>'Enviado (stub producción).',
            'mode'=>'prod'
        ];
    }
    private function generateCufe(array $f): string {
        $raw=($f['numero']??'').($f['fecha']??date('Y-m-d')).number_format((float)($f['total']??0),2,'.','').$this->cfg['nit'].($this->cfg['pin']??'').uniqid('dian',true);
        return strtoupper(hash('sha384',$raw));
    }
}
