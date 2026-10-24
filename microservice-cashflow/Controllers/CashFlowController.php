<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Model.php';

class MovementModel extends Model {

    protected string $table = 'movimientos';

    public function create(array $d): int {
        
        $this->db->prepare("
                            INSERT INTO movimientos 
                            (tipo,descripcion,monto,categoria,fecha,usuario_id,referencia) 
                            VALUES (?,?,?,?,?,?,?)
                            ")->execute([
                                        $d['tipo'],
                                        $d['descripcion'],
                                        $d['monto'],
                                        $d['categoria']??null,
                                        $d['fecha']??date('Y-m-d'),
                                        $d['usuario_id']??null,
                                        $d['referencia']??null]
                                        );
        
        return (int)$this->db->lastInsertId();
    }
    public function filter(?string $tipo = null, ?string $desde = null, ?string $hasta = null): array {
        
        $sql= "
                SELECT * FROM movimientos 
                WHERE 1=1"; 
        $p=[];
        
        if($tipo){
            $sql.= "AND tipo=?";
            $p[]=$tipo;
        } 
        
        if($desde){
            $sql.=" AND fecha>=?";
            $p[]=$desde;
        } 
        if($hasta){
            $sql.=" AND fecha<=?";
            $p[]=$hasta;
        }
        $sql.=" 
                ORDER BY 
                fecha DESC,
                id DESC"; 
        
        $s=$this->db->prepare($sql); 
        $s->execute($p); 
        
        return $s->fetchAll();
    }
    public function totals(): array {
        
        $i=(float)$this->db->query("
                                    SELECT COALESCE(SUM(monto),0) t 
                                    FROM movimientos 
                                    WHERE 
                                    tipo='ingreso'"
                                )->fetch()['t'];

        $e=(float)$this->db->query("
                                    SELECT COALESCE(SUM(monto),0) t 
                                    FROM movimientos 
                                    WHERE 
                                    tipo='egreso'"
                                )->fetch()['t'];
        
        return [
            'ingresos'=>$i,
            'egresos'=>$e,
            'saldo'=>$i-$e
        ];
    }
}
class CashFlowController extends Controller {

    private MovementModel $model;

    public function __construct(){
        parent::__construct();
        $this->model=new MovementModel();
    }

    public function index(): void {

        AuthMiddleware::handle('ingresos_egresos','read'); 
        
        $d=$this->input();
        
        Response::success([
                            'movimientos'=>$this->model->filter(
                                                                $d['tipo']??null,
                                                                $d['desde']??null,
                                                                $d['hasta']??null
                                                            ),
                            'totales'=>$this->model->totals()
                        ]);
    }
    public function store(): void {

        $auth=AuthMiddleware::handle('ingresos_egresos','create'); 
        
        $d=$this->input();
        
        if(empty($d['tipo'])||empty($d['descripcion'])||!isset($d['monto'])) {
            Response::error('tipo, descripcion y monto obligatorios.',422);
        }
        
        if(!in_array($d['tipo'],['ingreso','egreso'],true)) {
            Response::error('tipo inválido.',422);
        }
        
        $d['usuario_id']=$auth['id']; 
        
        Response::success([
            'id'=>$this->model->create($d)],
            'Registrado.',201
        );
    }
}
