<?php

/** @var \League\Plates\Template\Template $this */
/** @var \Cspray\WebhookTarget\Home\Controller\HomeTemplateData $data */

use function Stringy\create;

$this->layout('layouts::main');
?>

<section class="section">
    <div class="container is-max-tablet">
        <form method="post" action="/authenticate">
            <?php if ($data->isLoginError): ?>
            <div class="message is-danger">
                <div class="message-body">
                    That username or password is incorrect
                </div>
            </div>
            <?php endif ?>
            <div class="field">
                <label for="username" class="label">Username</label>
                <div class="control">
                    <input id="username" name="username" class="input" type="text" placeholder="Username" />
                </div>
            </div>
            <div class="field">
                <label for="password" class="label">Password</label>
                <div class="control">
                    <input id="password" name="password" class="input" type="password" />
                </div>
            </div>
            <div class="field">
                <div class="control">
                    <button type="submit" class="button is-primary">Login</button>
                </div>
            </div>
        </form>
    </div>
</section>