<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\Http\Exception;

use Cspray\WebhookTarget\Exception\Exception;

final class SessionNotSetOnRequest extends Exception {

    public static function fromRequestDoesNotHaveAppropriateSessionAttribute(string $sessionKey) : self {
        return new self(sprintf(
            'The %s Request attribute has not been set. Please set this value to an appropriate Session instance.',
            $sessionKey
        ));
    }

}