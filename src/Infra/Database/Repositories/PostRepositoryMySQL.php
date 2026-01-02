<?php

namespace App\Infra\Database\Repositories;

use App\Core\ValueObjects\UUID;
use App\Domain\Entities\Post;
use App\Domain\Repositories\PostRepository;
use App\Infra\Database\Connection;

class PostRepositoryMySQL implements PostRepository
{
    private \PDO $connection;

    public function __construct()
    {
        $this->connection = Connection::getInstance();
    }

    public function save(Post $post): void
    {
        $stmt = $this->connection->prepare(
            'INSERT INTO posts (id, title, content, author_id, created_at, updated_at)
             VALUES (:id, :title, :content, :author_id, :created_at, :updated_at)
             ON DUPLICATE KEY UPDATE title = :title, content = :content, updated_at = :updated_at'
        );

        $stmt->execute([
            ':id' => $post->getId()->__toString(),
            ':title' => $post->getTitle(),
            ':content' => $post->getContent(),
            ':author_id' => $post->getAuthorId()->__toString(),
            ':created_at' => $post->getCreatedAt()->format('Y-m-d H:i:s'),
            ':updated_at' => $post->getUpdatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    public function findById(string $id): ?Post
    {
        $stmt = $this->connection->prepare('SELECT * FROM posts WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function findAll(int $limit = 10, int $offset = 0): array
    {
        $stmt = $this->connection->prepare(
            'SELECT * FROM posts ORDER BY created_at DESC LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        return array_map(fn ($row) => $this->hydrate($row), $rows);
    }

    public function delete(string $id): void
    {
        $stmt = $this->connection->prepare('DELETE FROM posts WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    public function count(): int
    {
        $stmt = $this->connection->query('SELECT COUNT(*) as count FROM posts');
        return (int) $stmt->fetch()['count'];
    }

    private function hydrate(array $row): Post
    {
        return new Post(
            new UUID($row['id']),
            $row['title'],
            $row['content'],
            new UUID($row['author_id']),
            new \DateTime($row['created_at']),
            new \DateTime($row['updated_at'])
        );
    }
}
