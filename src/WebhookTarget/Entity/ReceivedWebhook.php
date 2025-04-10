<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\WebhookTarget\Entity;

use DateTimeImmutable;
use Psr\Http\Message\UriInterface;
use Ramsey\Uuid\UuidInterface;

final readonly class ReceivedWebhook {

    public function __construct(
        public UuidInterface $id,
        public string $protocol,
        public string $method,
        public UriInterface $uri,
        public array $headers,
        public string $body,
        public ?DateTimeImmutable $createdAt = null,
        public ?DateTimeImmutable $updatedAt = null
    ) {}

}
