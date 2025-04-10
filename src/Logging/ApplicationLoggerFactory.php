<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\Logging;

use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Cspray\AnnotatedContainer\Attribute\Service;
use Cspray\AnnotatedContainer\Profiles;
use Labrador\Logging\LoggerFactory;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Override;
use Psr\Log\LoggerInterface;
use function Amp\ByteStream\getStdout;

#[Service]
final class ApplicationLoggerFactory implements LoggerFactory {

    public function __construct(
        private readonly Profiles $activeProfiles
    ) {}

    #[Override]
    public function createLogger() : LoggerInterface {
        if ($this->activeProfiles->isActive('test') && !$this->activeProfiles->isActive('migrations')) {
            $handler = new TestHandler();
        } else {
            $handler = new StreamHandler(getStdout());
            $handler->setFormatter(new ConsoleFormatter());
        }

        return new Logger(
            'web-app',
            [$handler],
            [new PsrLogMessageProcessor()]
        );
    }

}