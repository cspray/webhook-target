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

$rawBody = $webhook->body;
try {
    $json = json_decode($rawBody, true, flags: JSON_THROW_ON_ERROR);
    $body = json_encode($json, flags: JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
} catch (JsonException $jsonException) {
    $body = '<<< ERROR_JSON_DECODE >>>' . PHP_EOL . $rawBody;
}

?>
<?= $this->e($body) ?>