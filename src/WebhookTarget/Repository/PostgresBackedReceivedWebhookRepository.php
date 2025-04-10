<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\WebhookTarget\Repository;

use Amp\Postgres\PostgresConnection;
use Cspray\WebhookTarget\Autowire\Repository;
use Cspray\WebhookTarget\WebhookTarget\Entity\ReceivedWebhook;
use League\Uri\Http;
use Ramsey\Uuid\Uuid;

#[Repository]
final class PostgresBackedReceivedWebhookRepository implements ReceivedWebhookRepository {
    public function __construct(
        private readonly PostgresConnection $connection,
    ) {}

    public function save(ReceivedWebhook $receivedWebhook) : ReceivedWebhook {
        $sql = <<<SQL
        INSERT INTO received_webhook (id, protocol, method, uri, headers, body)
        VALUES (:id, :protocol, :method, :uri, :headers, :body)
        SQL;

        $this->connection->execute($sql, [
            'id' => $receivedWebhook->id->toString(),
            'protocol' => $receivedWebhook->protocol,
            'method' => $receivedWebhook->method,
            'uri' => (string) $receivedWebhook->uri,
            'headers' => json_encode($receivedWebhook->headers),
            'body' => $receivedWebhook->body
        ]);

        return $receivedWebhook;
    }

    public function fetchAll() : \Generator {
        $result = $this->connection->execute('SELECT * FROM received_webhook');
        while ($row = $result->fetchRow()) {
            yield new ReceivedWebhook(
                Uuid::fromString($row['id']),
                $row['protocol'],
                $row['method'],
                Http::new($row['uri']),
                json_decode($row['headers'], true),
                $row['body'],
                new \DateTimeImmutable($row['created_at'], new \DateTimeZone('UTC')),
                new \DateTimeImmutable($row['updated_at'], new \DateTimeZone('UTC'))
            );
        }
    }
}