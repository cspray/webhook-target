<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\Autowire;

use Cspray\AnnotatedContainer\Attribute\ServiceAttribute;
use Override;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class Repository implements ServiceAttribute {

    /**
     * @param list<non-empty-string> $profiles
     */
    public function __construct(
        private readonly array $profiles = []
    ) {}

    #[Override]
    public function profiles() : array {
        return $this->profiles;
    }

    #[Override]
    public function isPrimary() : bool {
        return false;
    }

    #[Override]
    public function name() : ?string {
        return null;
    }
}