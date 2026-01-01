<?php

namespace App\Domain\Entities;

use App\Core\Exceptions\ValidationException;
use App\Core\ValueObjects\UUID;

class Post
{
    private UUID $id;
    private string $title;
    private string $content;
    private UUID $authorId;
    private \DateTime $createdAt;
    private \DateTime $updatedAt;

    public function __construct(
        UUID $id,
        string $title,
        string $content,
        UUID $authorId,
        ?\DateTime $createdAt = null,
        ?\DateTime $updatedAt = null
    ) {
        if (empty($title) || strlen($title) < 5) {
            throw new ValidationException(null, 'Title must be at least 5 characters', 401);
        }
        if (empty($content) || strlen($content) < 10) {
            throw new ValidationException(null, 'Content must be at least 10 characters', 401);
        }

        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->authorId = $authorId;
        $this->createdAt = $createdAt ?? new \DateTime();
        $this->updatedAt = $updatedAt ?? new \DateTime();
    }

    public function getId(): UUID
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getAuthorId(): UUID
    {
        return $this->authorId;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function update(string $title, string $content): void
    {
        if (empty($title) || strlen($title) < 5) {
            throw new ValidationException(null, 'Title must be at least 5 characters', 401);
        }
        if (empty($content) || strlen($content) < 10) {
            throw new ValidationException(null, 'Content must be at least 10 characters', 401);
        }

        $this->title = $title;
        $this->content = $content;
        $this->updatedAt = new \DateTime();
    }

    public function toArray(bool $includeContent = false): array
    {
        $data = [
            'id' => $this->id->__toString(),
            'title' => $this->title,
            'author_id' => $this->authorId->__toString(),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];

        if ($includeContent) {
            $data['content'] = $this->content;
        }

        return $data;
    }
}
