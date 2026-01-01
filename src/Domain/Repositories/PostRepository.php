<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Post;

interface PostRepository
{
    public function save(Post $post): void;
    public function findById(string $id): ?Post;
    public function findAll(int $limit = 10, int $offset = 0): array;
    public function delete(string $id): void;
    public function count(): int;
}
