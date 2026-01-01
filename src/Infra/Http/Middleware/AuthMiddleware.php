<?php

namespace App\Infra\Http\Middleware;

use App\Infra\Auth\JWTManager;

class AuthMiddleware
{
    private JWTManager $jwtManager;

    public function __construct()
    {
        $this->jwtManager = new JWTManager();
    }

    public function handle(): void
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (empty($authHeader)) {
            http_response_code(401);
            echo json_encode(['error' => 'Missing authorization header']);
            exit;
        }

        if (!preg_match('/Bearer\s+(.+)/', $authHeader, $matches)) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid authorization header']);
            exit;
        }

        try {
            $token = $matches[1];
            $payload = $this->jwtManager->verify($token);
            $_REQUEST['user'] = $payload;
        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid token: ' . $e->getMessage()]);
            exit;
        }
    }
}
