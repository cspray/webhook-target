<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\WebhookTarget\Repository;

use Cspray\WebhookTarget\Autowire\Repository;
use Cspray\WebhookTarget\WebhookTarget\Entity\ReceivedWebhook;

#[Repository]
interface ReceivedWebhookRepository {

    public function save(ReceivedWebhook $receivedWebhook) : ReceivedWebhook;

    /**
     * @return \Generator<int, ReceivedWebhook>
     */
    public function fetchAll() : \Generator;

}