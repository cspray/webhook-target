<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\Tests\Helper;

use Cspray\WebhookTarget\ApplicationBootstrap;
use Cspray\AnnotatedContainer\AnnotatedContainer;
use Cspray\AnnotatedContainer\Profiles;

final class ContainerHelper {

    private function __construct() {}

    public static function bootstrapTestContainer(array $profiles) : AnnotatedContainer {
        return (new ApplicationBootstrap())->bootstrapContainer(Profiles::fromList($profiles));
    }

}