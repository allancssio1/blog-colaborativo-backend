<?php

namespace App\Infra\Auth;

use App\Core\Exceptions\ValidationException;
use App\Infra\Env\Env;

class JWTManager
{
    private string $secret;
    private int $expiresIn;

    public function __construct(?string $secret = null, int $expiresIn = 60 * 60 * 24)
    {
        $this->secret = $secret ?? Env::getString('JWT_SECRET');
        $this->expiresIn = $expiresIn;
    }

    public function generate(array $payload): string
    {
        $header = json_encode([
          'typ' => 'JWT',
          'alg' => 'HS256',
        ]);
        $payload['iat'] = time();
        $payload['exp'] = time() + $this->expiresIn;
        $payload_encoded = json_encode($payload);

        $headerEncoded = $this->base64UrlEncode($header);
        $payloadEncoded = $this->base64UrlEncode($payload_encoded);
        $signature = $this->createSignature("{$headerEncoded}.{$payloadEncoded}");

        return "{$headerEncoded}.{$payloadEncoded}.{$signature}";
    }

    public function verify(string $token): array
    {
        $part = explode('.', $token);

        if (count($part) !== 3) {
            throw new ValidationException(null, 'Invalid token', 401);
        }

        [$headerEncoded, $payloadEncoded, $signatureProvided] = $part;
        $signature = $this->createSignature("{$headerEncoded}.{$payloadEncoded}");

        if (!hash_equals($signatureProvided, $signature)) {
            throw new ValidationException(null, 'Invalid token', 401);
        }

        $payload = json_decode($this->base64UrlDecode($payloadEncoded), true);

        if ($payload['exp'] < time()) {
            throw new ValidationException(null, 'Token expired', 401);
        }

        return $payload;
    }

    private function createSignature(string $data): string
    {
        $hash = hash_hmac('sha256', $this->secret, true);
        return $this->base64UrlEncode($hash);
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }

}
