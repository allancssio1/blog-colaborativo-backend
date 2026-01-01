<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Infra\Env\Env;


// Load environment variables (if needed in the future)
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

Env::validate();

// Basic headers for API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *' . Env::getString('CORS_ORIGIN'));
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$router = new \App\Infra\Http\Router();
$userRepository = new \App\Infra\Database\Repositories\MySQLUserRepository();
$postRepository = new \App\Infra\Database\Repositories\MySQLPostRepository();
$jwtManager = new \App\Infra\Auth\JWTManager();
$validator = new \App\Infra\Http\Validators\FormValidator();

// Controllers
$authController = new \App\Infra\Http\Controllers\AuthController(
    new \App\Domain\UseCases\Auth\RegisterUserUseCase($userRepository),
    new \App\Domain\UseCases\Auth\LoginUserUseCase($userRepository),
    $jwtManager,
    $validator
);

$postController = new \App\Infra\Http\Controllers\PostController(
    new \App\Domain\UseCases\Post\CreatePostUseCase($postRepository),
    new \App\Domain\UseCases\Post\ListPostsUseCase($postRepository),
    new \App\Domain\UseCases\Post\GetPostUseCase($postRepository),
    new \App\Domain\UseCases\Post\UpdatePostUseCase($postRepository),
    new \App\Domain\UseCases\Post\DeletePostUseCase($postRepository),
    $validator
);

// Rotas públicas
$router->post('/auth/register', [$authController, 'register']);
$router->post('/auth/login', [$authController, 'login']);
$router->get('/posts', [$postController, 'list']);
$router->get('/posts/{id}', [$postController, 'get']);

// Rotas protegidas
$router->post('/posts', function () use ($postController) {
    (new \App\Infra\Http\Middleware\AuthMiddleware())->handle();
    $postController->create();
});

$router->put('/posts/{id}', function ($id) use ($postController) {
    (new \App\Infra\Http\Middleware\AuthMiddleware())->handle();
    $postController->update($id);
});

$router->delete('/posts/{id}', function ($id) use ($postController) {
    (new \App\Infra\Http\Middleware\AuthMiddleware())->handle();
    $postController->delete($id);
});

// Dispatch
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$router->dispatch($method, $path, []);
