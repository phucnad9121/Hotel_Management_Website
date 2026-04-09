<?php

class JWT {
    private static $algo = 'HS256';

    public static function encode(array $payload, $expiresIn = 3600, $secret = null) {
        $secret = self::secret($secret);
        $now = time();

        if (!isset($payload['iat'])) {
            $payload['iat'] = $now;
        }
        if (!isset($payload['exp'])) {
            $payload['exp'] = $now + (int) $expiresIn;
        }

        $header = ['typ' => 'JWT', 'alg' => self::$algo];

        $segments = [
            self::base64UrlEncode(json_encode($header)),
            self::base64UrlEncode(json_encode($payload)),
        ];

        $signature = hash_hmac('sha256', implode('.', $segments), $secret, true);
        $segments[] = self::base64UrlEncode($signature);

        return implode('.', $segments);
    }

    public static function decode($token, $secret = null) {
        $secret = self::secret($secret);
        if (!is_string($token) || $token === '') {
            return null;
        }

        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        list($headB64, $bodyB64, $sigB64) = $parts;

        $header = json_decode(self::base64UrlDecode($headB64), true);
        $payload = json_decode(self::base64UrlDecode($bodyB64), true);
        $signature = self::base64UrlDecode($sigB64);

        if (!is_array($header) || !is_array($payload) || !is_string($signature)) {
            return null;
        }

        if (($header['alg'] ?? '') !== self::$algo) {
            return null;
        }

        $validSignature = hash_hmac('sha256', $headB64 . '.' . $bodyB64, $secret, true);
        if (!hash_equals($validSignature, $signature)) {
            return null;
        }

        if (isset($payload['exp']) && time() >= (int) $payload['exp']) {
            return null;
        }

        return (object) $payload;
    }

    public static function refresh($token, $expiresIn = 3600, $secret = null) {
        $decoded = self::decode($token, $secret);
        if (!$decoded) {
            return null;
        }

        $payload = (array) $decoded;
        unset($payload['iat'], $payload['exp']);

        return self::encode($payload, $expiresIn, $secret);
    }

    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode($data) {
        $remainder = strlen($data) % 4;
        if ($remainder > 0) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }

    private static function secret($secret) {
        if (is_string($secret) && $secret !== '') {
            return $secret;
        }

        if (defined('API_JWT_SECRET')) {
            return API_JWT_SECRET;
        }

        $env = getenv('API_JWT_SECRET');
        if (is_string($env) && $env !== '') {
            return $env;
        }

        return 'hotel-management-api-secret-key-change-me';
    }
}
