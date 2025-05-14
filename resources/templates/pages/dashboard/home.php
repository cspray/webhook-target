<?php declare(strict_types=1);

/** @var \League\Plates\Template\Template $this */
/** @var \Cspray\WebhookTarget\Dashboard\Template\DashboardTemplateData $data */

$this->layout('layouts::main');


?>

<section class="section">
    <div class="container is-max-tablet">
        <?php foreach ($data->receivedWebhooks as $webhook): ?>
            <div class="received-webhook mb-3">
                <div>ID: <span class="has-text-weight-bold"><?= $this->e($webhook->id->toString()) ?></span></div>
                <pre><code><?= $this->fetch('components::received-webhook', ['webhook' => $webhook]) ?></code></pre>
            </div>
        <?php endforeach ?>
    </div>
</section>