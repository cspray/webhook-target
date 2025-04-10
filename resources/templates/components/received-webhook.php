<?php

/** @var \Cspray\WebhookTarget\WebhookTarget\Entity\ReceivedWebhook $webhook */

?>
<?= $webhook->method ?> <?= $webhook->uri->getPath() ?> HTTP/<?= $webhook->protocol ?>

<?php foreach ($webhook->headers as $header => $values): ?>
<?php foreach ($values as $value): ?>
<?= $header ?>: <?= $value ?>

<?php endforeach ?>
<?php endforeach ?>

<?= $webhook->body ?>