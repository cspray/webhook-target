<?php

/** @var \Cspray\WebhookTarget\WebhookTarget\Entity\ReceivedWebhook $webhook */

?>
<?= $this->e($webhook->method) ?> <?= $this->e($webhook->uri->getPath()) ?> HTTP/<?= $this->e($webhook->protocol) ?>

<?php foreach ($webhook->headers as $header => $values): ?>
<?php foreach ($values as $value): ?>
<?= $this->e($header) ?>: <?= $this->e($value) ?>

<?php endforeach ?>
<?php endforeach ?>


<?php

$json = json_decode($webhook->body, true, flags: JSON_THROW_ON_ERROR);
$body = json_encode($json, flags: JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);

?>
<?= $this->e($body) ?>