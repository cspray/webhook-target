<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\Dashboard\Template;

use Cspray\WebhookTarget\WebhookTarget\Entity\ReceivedWebhook;
use Labrador\Template\TemplateData;

final readonly class DashboardTemplateData implements TemplateData {

    public function __construct(
        /** @var \Generator<int, ReceivedWebhook> */
        public \Generator $receivedWebhooks
    ) {}

}