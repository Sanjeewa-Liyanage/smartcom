<?php

namespace App\Core;

/**
 * JWT — Pure PHP HS256 JSON Web Token
 *
 * No external libraries required.
 * Call JWT::init() once at bootstrap (public/index.php).
 *
 * Usage:
 *   $token = JWT::encode(['user_id' => 1, 'role' => 'admin', 'name' => 'Tharushika']);
 *   $data  = JWT::decode($token);   // returns payload array or null if invalid/expired
 */
class JWT
{
    private static string $secret = '';

    /** Must be called once after JWT_SECRET constant is defined */
    public static function init(): void
    {
        self::$secret = JWT_SECRET;
    }

    /**
     * Create a signed JWT token.
     *
     * @param array $payload Key-value data to embed (do NOT put sensitive data here)
     * @return string  Signed JWT string
     */
    public static function encode(array $payload): string
    {
        $header  = self::b64u(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload['iat'] = time();
        $payload['exp'] = time() + JWT_EXPIRY;
        $body    = self::b64u(json_encode($payload));
        $sig     = self::b64u(hash_hmac('sha256', "$header.$body", self::$secret, true));

        return "$header.$body.$sig";
    }

    /**
     * Decode and verify a JWT token.
     *
     * @param  string     $token
     * @return array|null Decoded payload, or null if invalid / expired / tampered
     */
    public static function decode(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$header, $body, $sig] = $parts;

        // Verify signature (constant-time comparison to prevent timing attacks)
        $expected = self::b64u(hash_hmac('sha256', "$header.$body", self::$secret, true));
        if (!hash_equals($expected, $sig)) {
            return null;
        }

        // Decode payload
        $payload = json_decode(self::b64uDecode($body), true);
        if (!is_array($payload)) {
            return null;
        }

        // Check expiry
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return null;
        }

        return $payload;
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /** Base64URL-encode (RFC 4648) */
    private static function b64u(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /** Base64URL-decode */
    private static function b64uDecode(string $data): string
    {
        $pad  = strlen($data) % 4;
        $data = strtr($data, '-_', '+/');
        if ($pad) {
            $data .= str_repeat('=', 4 - $pad);
        }
        return base64_decode($data);
    }
}
