<?php declare(strict_types=1);

/** @var \League\Plates\Template\Template $this */
/** @var \Cspray\WebhookTarget\Dashboard\Template\DashboardTemplateData $data */

$this->layout('layouts::main');

$webhooks = iterator_to_array($data->receivedWebhooks);

?>

<section class="section">
    <div class="container is-max-tablet">
        <?php if ($webhooks === []): ?>
            <div class="message">
                <div class="message-body">
                    No webhooks have been received yet.
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($webhooks as $webhook): ?>
                <div class="received-webhook mb-3">
                    <div>ID: <span class="has-text-weight-bold"><?= $this->e($webhook->id->toString()) ?></span></div>
                    <pre><code><?= $this->fetch('components::received-webhook', ['webhook' => $webhook]) ?></code></pre>
                </div>
            <?php endforeach ?>
        <?php endif ?>
    </div>
</section>