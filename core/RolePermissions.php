<?php
class RolePermissions {
    private static array $matrix = [
        'administrador' => [
            'dashboard'=>['view'],
            'facturas'=>['create','read','process','dian'],
            'usuarios'=>['create','read','update','delete','roles'],
            'compras'=>['create','read','update','cancel'],
            'proveedores'=>['create','read','update','delete'],
            'ingresos_egresos'=>['create','read'],
            'informes'=>['generate','view'],
            'inventario'=>['create','read','update','delete','stock'],
            'pagos'=>['create','read','update','conciliar','pasarela'],
            'pedidos'=>['create','read','update','cancel'],
            'soporte'=>['view'],
            'config'=>['view','update'],
        ],
        'gerente' => [
            'dashboard'=>['view'],
            'facturas'=>['read','process'],
            'usuarios'=>['read'],
            'compras'=>['create','read','update','cancel'],
            'proveedores'=>['create','read','update'],
            'ingresos_egresos'=>['create','read'],
            'informes'=>['generate','view'],
            'inventario'=>['read','stock'],
            'pagos'=>['read','conciliar'],
            'pedidos'=>['create','read','update','cancel'],
            'config'=>['view','update'],
        ],
        'jefe' => [
            'dashboard'=>['view'],
            'facturas'=>['read'],
            'compras'=>['create','read','update'],
            'proveedores'=>['read'],
            'ingresos_egresos'=>['read'],
            'informes'=>['view'],
            'inventario'=>['read','stock'],
            'pagos'=>['read'],
            'pedidos'=>['create','read','update'],
            'config'=>['view','update'],
        ],
        'contable' => [
            'dashboard'=>['view'],
            'facturas'=>['create','read','process','dian'],
            'compras'=>['read'],
            'ingresos_egresos'=>['create','read'],
            'informes'=>['generate','view'],
            'pagos'=>['read','conciliar'],
            'pedidos'=>['read'],
            'config'=>['view','update'],
        ],
        'cajero' => [
            'dashboard'=>['view'],
            'facturas'=>['create','read'],
            'inventario'=>['read'],
            'pedidos'=>['create','read'],
            'pagos'=>['create','read','pasarela'],
            'config'=>['view','update'],
        ],
        'bodeguero' => [
            'dashboard'=>['view'],
            'proveedores'=>['read'],
            'inventario'=>['create','read','update','stock'],
            'compras'=>['read','update'],
            'config'=>['view','update'],
        ],
        'soporte' => [
            'dashboard'=>['view'],
            'usuarios'=>['read'],
            'soporte'=>['view','create'],
            'config'=>['view','update'],
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
            'dashboard'=>['Dashboard','home.png','dashboard'],
            'facturas'=>['Facturación','facturacion.png','facturas'],
            'usuarios'=>['Usuarios','users.png','usuarios'],
            'compras'=>['Compras a Proveedores','comprasProveedores.png','compras'],
            'proveedores'=>['Proveedores','users-collaboration.png','proveedores'],
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
