<?php
return [
    'app_name'    => 'ContaTech',
    'app_version' => '2.0.0',
    'base_url'    => '/ContaTech1',
    'api_url'     => '/ContaTech1/api',
    'timezone'    => 'America/Bogota',
    'jwt_secret'  => 'ContaTech_JWT_Secret_Key_2026_ChangeInProduction_X9kL2mP',
    'jwt_issuer'  => 'ContaTech',
    'jwt_expire'  => 28800,
    'jwt_algo'    => 'HS256',
    'bold' => [
        'api_url' => 'https://api.bold.co/v1',
        'api_key' => 'BOLD_API_KEY_SANDBOX',
        'enabled' => true,
    ],
    'dian' => [
        'api_url' => 'https://catalogo-vpfe-hab.dian.gov.co',
        'nit' => '900000000',
        'software_id' => 'SOFTWARE_ID_DIAN',
        'pin' => '12345',
        'enabled' => true,
        'test_mode' => true,
    ],
    'roles' => [
        'administrador' => 'Administrador',
        'gerente' => 'Gerente',
        'jefe' => 'Jefe',
        'contable' => 'Contable',
        'soporte' => 'Soporte',
        'cajero' => 'Cajero',
        'bodeguero' => 'Bodeguero',
    ],
];
