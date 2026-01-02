<?php

namespace App\Infra\Http\Controllers;

use App\Core\Exceptions\DomainException;
use App\Core\Exceptions\ValidationException;
use App\Domain\UseCases\Auth\LoginUserUseCase;
use App\Domain\UseCases\Auth\RegisterUserUseCase;
use App\Infra\Auth\JWTManager;
use App\Infra\Http\Validators\FormValidator;

class AuthController
{
    private RegisterUserUseCase $registerUseCase;
    private LoginUserUseCase $loginUseCase;
    private JWTManager $jwtManager;
    private FormValidator $validator;

    public function __construct(
        RegisterUserUseCase $registerUseCase,
        LoginUserUseCase $loginUseCase,
        JWTManager $jwtManager,
        FormValidator $validator
    ) {
        $this->registerUseCase = $registerUseCase;
        $this->loginUseCase = $loginUseCase;
        $this->jwtManager = $jwtManager;
        $this->validator = $validator;
    }

    public function register(): void
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        // Validação simplificada
        $errors = $this->validator->validate($data, [
          'name' => ['required', 'min:3'],
          'email' => ['required',  'email'],
          'password' => ['required'],
        ]);

        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['errors' => $errors]);
            return;
        }

        try {
            // O RegisterUserUseCase já verifica se o usuário existe


            $user = $this->registerUseCase->execute(
                $data['name'],
                $data['email'],
                $data['password']
            );

            http_response_code(201);
            echo json_encode([
              'message' => 'User registered successfully',
              'user' => $user->toArray(),
            ]);
        } catch (DomainException $e) {
            http_response_code($e->getStatusCode());
            echo json_encode(['error' => $e->getMessage()]);
        } catch (ValidationException $e) {
            http_response_code(422);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function login(): void
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $errors = $this->validator->validate($data, [
          'email' => ['required'],
          'password' => ['required'],
        ]);

        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['errors' => $errors]);
            return;
        }

        try {
            $userData = $this->loginUseCase->execute(
                $data['email'],
                $data['password']
            );

            $token = $this->jwtManager->generate($userData);

            http_response_code(200);
            echo json_encode([
              'message' => 'Login successful',
              'token' => $token,
              'user' => $userData,
            ]);
        } catch (DomainException $e) {
            http_response_code($e->getStatusCode());
            echo json_encode(['error' => $e->getMessage()]);
        } catch (ValidationException $e) {
            http_response_code(422);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
