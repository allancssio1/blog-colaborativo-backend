<?php

namespace App\Infra\Http\Controllers;

use App\Core\Exceptions\DomainException;
use App\Core\Exceptions\NotFoundException;
use App\Domain\UseCases\Post\{
    CreatePostUseCase,
    DeletePostUseCase,
    GetPostUseCase,
    ListPostsUseCase,
    UpdatePostUseCase
};
use App\Infra\Http\Validators\FormValidator;

class PostController
{
    private CreatePostUseCase $createUseCase;
    private ListPostsUseCase $listUseCase;
    private GetPostUseCase $getUseCase;
    private UpdatePostUseCase $updateUseCase;
    private DeletePostUseCase $deleteUseCase;
    private FormValidator $validator;

    public function __construct(
        CreatePostUseCase $createUseCase,
        ListPostsUseCase $listUseCase,
        GetPostUseCase $getUseCase,
        UpdatePostUseCase $updateUseCase,
        DeletePostUseCase $deleteUseCase,
        FormValidator $validator
    ) {
        $this->createUseCase = $createUseCase;
        $this->listUseCase = $listUseCase;
        $this->getUseCase = $getUseCase;
        $this->updateUseCase = $updateUseCase;
        $this->deleteUseCase = $deleteUseCase;
        $this->validator = $validator;
    }

    public function list(): void
    {
        try {
            $limit = (int) ($_GET['limit'] ?? 10);
            $offset = (int) ($_GET['offset'] ?? 0);

            $result = $this->listUseCase->execute($limit, $offset);

            http_response_code(200);
            echo json_encode($result);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function get(string $id): void
    {
        try {
            $post = $this->getUseCase->execute($id);
            http_response_code(200);
            echo json_encode($post);
        } catch (NotFoundException $e) {
            http_response_code($e->getStatusCode());
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function create(): void
    {
        if (!isset($_REQUEST['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $errors = $this->validator->validate($data, [
            'title' => ['required', 'min:5'],
            'content' => ['required', 'min:10'],
        ]);

        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['errors' => $errors]);
            return;
        }

        try {
            $post = $this->createUseCase->execute(
                $data['title'],
                $data['content'],
                $_REQUEST['user']['id']
            );

            http_response_code(201);
            echo json_encode([
                'message' => 'Post created successfully',
                'post' => $post->toArray(true),
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function update(string $id): void
    {
        if (!isset($_REQUEST['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $errors = $this->validator->validate($data, [
            'title' => ['required', 'min:5'],
            'content' => ['required', 'min:10'],
        ]);

        if (!empty($errors)) {
            http_response_code(422);
            echo json_encode(['errors' => $errors]);
            return;
        }

        try {
            $post = $this->updateUseCase->execute(
                $id,
                $data['title'],
                $data['content'],
                $_REQUEST['user']['id']
            );

            http_response_code(200);
            echo json_encode([
                'message' => 'Post updated successfully',
                'post' => $post,
            ]);
        } catch (NotFoundException | DomainException $e) {
            http_response_code($e->getStatusCode());
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function delete(string $id): void
    {
        if (!isset($_REQUEST['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        try {
            $this->deleteUseCase->execute($id, $_REQUEST['user']['id']);

            http_response_code(204);
        } catch (NotFoundException | DomainException $e) {
            http_response_code($e->getStatusCode());
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
