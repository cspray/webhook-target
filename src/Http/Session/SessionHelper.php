<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\Http\Session;

use Amp\Http\Server\Request;
use Amp\Http\Server\Session\Session;
use Cspray\WebhookTarget\Http\Exception\SessionNotSetOnRequest;
use Cspray\AnnotatedContainer\Attribute\Service;

#[Service]
final class SessionHelper {

    public function get(Request $request, SessionAttribute $attribute) : ?string {
        return $this->session($request)->get($attribute->name);
    }

    public function set(Request $request, SessionAttribute $attribute, string $value) : void {
        $this->session($request)->set($attribute->name, $value);
    }

    public function unset(Request $request, SessionAttribute $attribute) : void {
        $this->session($request)->unset($attribute->name);
    }

    private function session(Request $request) : Session {
        if (!$request->hasAttribute(Session::class)) {
            throw SessionNotSetOnRequest::fromRequestDoesNotHaveAppropriateSessionAttribute(Session::class);
        }

        $session = $request->getAttribute(Session::class);
        assert($session instanceof Session);

        return $session;
    }

}