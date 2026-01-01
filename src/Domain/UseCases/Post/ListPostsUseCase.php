<?php

namespace App\Domain\UseCases\Post;

use App\Domain\Repositories\PostRepository;

class ListPostsUseCase
{
    public function __construct(private PostRepository $postRepository)
    {
    }

    public function execute(int $limit = 10, int $offset = 0): array
    {
        $posts = $this->postRepository->findAll($limit, $offset);
        $total = $this->postRepository->count();

        return [
            'data' => array_map(fn ($post) => $post->toArray(), $posts),
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset,
        ];
    }
}
