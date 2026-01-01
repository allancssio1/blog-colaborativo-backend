<?php

namespace App\Domain\UseCases\Post;

use App\Core\Exceptions\NotFoundException;
use App\Domain\Repositories\PostRepository;

class GetPostUseCase
{
    public function __construct(private PostRepository $postRepository)
    {
    }

    public function execute(string $postId): array
    {
        $post = $this->postRepository->findById($postId);

        if ($post === null) {
            throw new NotFoundException('Post not found');
        }

        return $post->toArray(true);
    }
}
