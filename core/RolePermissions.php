<?php
class RolePermissions {
    private static array $matrix = [
        'administrador' => [
            'dashboard'=>['view'],'usuarios'=>['create','read','update','delete','roles'],
            'proveedores'=>['create','read','update','delete'],'inventario'=>['create','read','update','delete','stock'],
            'pagos'=>['create','read','update','conciliar','pasarela'],'facturas'=>['create','read','process','dian'],
            'pedidos'=>['create','read','update','cancel'],'compras'=>['create','read','update','cancel'],
            'ingresos_egresos'=>['create','read'],'informes'=>['generate','view'],'soporte'=>['view'],'config'=>['view','update'],
        ],
        'gerente' => [
            'dashboard'=>['view'],'usuarios'=>['read'],'proveedores'=>['create','read','update'],
            'inventario'=>['read','stock'],'pagos'=>['read','conciliar'],'facturas'=>['read','process'],
            'pedidos'=>['create','read','update','cancel'],'compras'=>['create','read','update','cancel'],
            'ingresos_egresos'=>['create','read'],'informes'=>['generate','view'],
        ],
        'jefe' => [
            'dashboard'=>['view'],'proveedores'=>['read'],'inventario'=>['read','stock'],'pagos'=>['read'],
            'facturas'=>['read'],'pedidos'=>['create','read','update'],'compras'=>['create','read','update'],
            'ingresos_egresos'=>['read'],'informes'=>['view'],
        ],
        'contable' => [
            'dashboard'=>['view'],'facturas'=>['create','read','process','dian'],'pagos'=>['read','conciliar'],
            'ingresos_egresos'=>['create','read'],'informes'=>['generate','view'],'pedidos'=>['read'],'compras'=>['read'],
        ],
        'cajero' => [
            'dashboard'=>['view'],'pedidos'=>['create','read'],'facturas'=>['create','read'],
            'pagos'=>['create','read','pasarela'],'inventario'=>['read'],
        ],
        'bodeguero' => [
            'dashboard'=>['view'],'inventario'=>['create','read','update','stock'],
            'compras'=>['read','update'],'proveedores'=>['read'],
        ],
        'soporte' => [
            'dashboard'=>['view'],'usuarios'=>['read'],'soporte'=>['view','create'],
        ],
    ];
    public static function can(string $role, string $module, string $action = 'read'): bool {
        
        $role = strtolower($role);
        
        if (!isset(self::$matrix[$role][$module])) {
            return false;
        }
        
        return in_array($action, self::$matrix[$role][$module], true);
    }
    public static function modulesFor(string $role): array {

        return array_keys(self::$matrix[strtolower($role)] ?? []);
    
    }

    public static function menuFor(string $role): array {

        $labels = [
            'dashboard'=>['Home','home.png','dashboard'],
            'facturas'=>['Facturación','facturacion.png','facturas'],
            'usuarios'=>['Usuarios','configuracion.png','usuarios'],
            'compras'=>['Compras','comprasProveedores.png','compras'],
            'proveedores'=>['Proveedores','comprasProveedores.png','proveedores'],
            'ingresos_egresos'=>['Ingresos / Egresos','ingresosEgresos.png','ingresos-egresos'],
            'informes'=>['Informes','reportes.png','informes'],
            'inventario'=>['Inventario','inventario.png','inventario'],
            'pagos'=>['Pagos','conciliacionBancaria.png','pagos'],
            'pedidos'=>['Pedidos / Ventas','cuentasCobrar.png','pedidos'],
            'soporte'=>['Soporte','soporte.png','soporte'],
            'config'=>['Configuración','configuracion.png','config'],
        ];
        
        $menu = [];

        foreach (self::modulesFor($role) as $mod) {

            if (isset($labels[$mod])) {
                $menu[] = [
                            'key'=>$mod,
                            'label'=>$labels[$mod][0],
                            'icon'=>$labels[$mod][1],
                            'route'=>$labels[$mod][2]
                        ];
            }
        }

        return $menu;
    }
}
