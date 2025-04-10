<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\Home\Controller;

use Labrador\Template\TemplateData;

final class HomeTemplateData implements TemplateData {

    public function __construct(
        public readonly bool $isLoginError = false
    ) {}

}