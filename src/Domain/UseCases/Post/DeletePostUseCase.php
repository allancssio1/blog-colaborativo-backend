<?php

namespace App\Domain\UseCases\Post;

use App\Core\Exceptions\DomainException;
use App\Core\Exceptions\NotFoundException;
use App\Domain\Repositories\PostRepository;

class DeletePostUseCase
{
    public function __construct(private PostRepository $postRepository)
    {
    }

    public function execute(string $postId, string $userId): void
    {
        $post = $this->postRepository->findById($postId);

        if ($post === null) {
            throw new NotFoundException('Post not found');
        }

        if ($post->getAuthorId()->__toString() !== $userId) {
            throw new DomainException('You can only delete your own posts', 403);
        }

        $this->postRepository->delete($postId);
    }
}
