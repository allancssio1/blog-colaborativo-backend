<?php

namespace App\Domain\UseCases\Post;

use App\Core\ValueObjects\UUID;
use App\Domain\Entities\Post;
use App\Domain\Repositories\PostRepository;

class CreatePostUseCase
{
    public function __construct(private PostRepository $postRepository)
    {
    }

    public function execute(string $title, string $content, string $authorId): Post
    {
        $post = new Post(
            new UUID(null),
            $title,
            $content,
            new UUID($authorId)
        );

        $this->postRepository->save($post);

        return $post;
    }
}
