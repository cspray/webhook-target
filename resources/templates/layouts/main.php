<?php declare(strict_types=1);

/** @var \League\Plates\Template\Template $this */

$this->layout('layouts::base');

?>
<header>
    <?= $this->fetch('components::navbar') ?>
</header>
<main>
    <?= $this->section('content') ?>
</main>
<footer class="footer">
    <div class="container has-text-centered">
        Copyright <?= (new DateTimeImmutable())->format('Y') ?> Charles Sprayberry
    </div>
</footer>
