<?php
class JWT {

    private static function config(): array { 
        return require __DIR__ . '/../config/app.php'; 
    }
    
    private static function b64e(string $d): string { 
        return rtrim(strtr(base64_encode($d), '+/', '-_'), '='); 
    }
    
    private static function b64d(string $d): string {
        $r = strlen($d) % 4; if ($r) $d .= str_repeat('=', 4 - $r);
        return base64_decode(strtr($d, '-_', '+/'));
    }

    public static function encode(array $payload): string {
        $cfg = self::config();
        $header = ['typ' => 'JWT', 'alg' => $cfg['jwt_algo']];
        $now = time();
        $payload = array_merge(
            $payload, 
            [
                'iss' => $cfg['jwt_issuer'], 
                'iat' => $now, 
                'exp' => $now + (int)$cfg['jwt_expire']
            ]
        );

        $seg = [self::b64e(json_encode($header)), self::b64e(json_encode($payload))];
        $sig = hash_hmac('sha256', implode('.', $seg), $cfg['jwt_secret'], true);
        $seg[] = self::b64e($sig);

        return implode('.', $seg);
    }
    
    public static function decode(string $token): array {

        $cfg = self::config();

        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            throw new Exception('Token JWT mal formado', 401);
        }
        [$h, $p, $s] = $parts;
        $expected = self::b64e(hash_hmac('sha256', "$h.$p", $cfg['jwt_secret'], true));
        
        if (!hash_equals($expected, $s)){
            throw new Exception('Firma JWT inválida', 401);
        }

        $payload = json_decode(self::b64d($p), true);
        
        if (!$payload) {
            throw new Exception('Payload JWT inválido', 401);
        }
        
        if (isset($payload['exp']) && time() >= (int)$payload['exp']){
            throw new Exception('Token expirado. Inicie sesión nuevamente.', 401);
        }

        return $payload;
    }

    public static function fromRequest(): ?string {

        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? null;
        
        if (!$header && isset($_COOKIE['contatech_token'])) {
            return $_COOKIE['contatech_token'];
        }
        
        if (!$header && isset($_GET['token'])) {
            return $_GET['token'];
        }
        
        if ($header && preg_match('/Bearer\s+(\S+)/i', $header, $m)) {
            return $m[1];
        }
        
        return null;
    }
}
