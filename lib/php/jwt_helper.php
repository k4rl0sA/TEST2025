<?php
// Librería JWT simple basada en firebase/php-jwt (solo para ejemplo, usa la oficial en producción)
// https://github.com/firebase/php-jwt

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data) {
    return base64_decode(strtr($data, '-_', '+/'));
}

function jwt_encode($payload, $key, $alg = 'HS256') {
    $header = ['typ' => 'JWT', 'alg' => $alg];
    $segments = [
        base64url_encode(json_encode($header)),
        base64url_encode(json_encode($payload))
    ];
    $signing_input = implode('.', $segments);
    $signature = '';
    switch ($alg) {
        case 'HS256':
            $signature = hash_hmac('sha256', $signing_input, $key, true);
            break;
        default:
            throw new Exception('Algoritmo no soportado');
    }
    $segments[] = base64url_encode($signature);
    return implode('.', $segments);
}

function jwt_decode($jwt, $key, $alg = 'HS256') {
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) return false;
    list($headb64, $bodyb64, $cryptob64) = $parts;
    $header = json_decode(base64url_decode($headb64), true);
    $payload = json_decode(base64url_decode($bodyb64), true);
    $sig = base64url_decode($cryptob64);
    $valid = false;
    switch ($alg) {
        case 'HS256':
            $valid = hash_equals(hash_hmac('sha256', "$headb64.$bodyb64", $key, true), $sig);
            break;
        default:
            return false;
    }
    if (!$valid) return false;
    // Validar expiración si existe
    if (isset($payload['exp']) && $payload['exp'] < time()) return false;
    return $payload;
}
