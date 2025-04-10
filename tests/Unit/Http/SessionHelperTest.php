<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\Tests\Unit\Http;

use Amp\Http\Server\Driver\Client;
use Amp\Http\Server\Request;
use Amp\Http\Server\Session\LocalSessionStorage;
use Amp\Http\Server\Session\Session;
use Amp\Http\Server\Session\SessionFactory;
use Amp\Http\Server\Session\SessionStorage;
use Cspray\WebhookTarget\Exception\Exception;
use Cspray\WebhookTarget\Http\Exception\SessionNotSetOnRequest;
use Cspray\WebhookTarget\Http\Session\SessionAttribute;
use Cspray\WebhookTarget\Http\Session\SessionHelper;
use Cspray\AssertThrows\ThrowableAssertTestCaseMethods;
use Labrador\TestHelper\KnownSessionIdGenerator;
use League\Uri\Http;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(SessionHelper::class)]
#[CoversClass(SessionNotSetOnRequest::class)]
#[CoversClass(Exception::class)]
final class SessionHelperTest extends TestCase {

    use ThrowableAssertTestCaseMethods;

    private SessionHelper $subject;
    private Session $session;
    private SessionStorage $storage;
    private Request $request;

    protected function setUp() : void {
        $this->subject = new SessionHelper();
        $this->request = new Request(
            $this->createMock(Client::class),
            'GET',
            Http::new('http://example.com')
        );
        $this->storage = new LocalSessionStorage();
        $this->session = (new SessionFactory(
            storage: $this->storage,
            idGenerator: new KnownSessionIdGenerator()
        ))->create('known-session-id');
    }

    public static function sessionNotSetProvider() : array {
        return [
            'get' => [
                function() {
                    return $this->subject->get($this->request, SessionAttribute::UserId);
                }
            ],
            'set' => [
                function() {
                    $this->subject->set($this->request, SessionAttribute::UserId, 'my-user-id');
                }
            ],
            'unset' => [
                function () {
                    $this->subject->unset($this->request, SessionAttribute::UserId);
                }
            ]
        ];
    }

    #[DataProvider('sessionNotSetProvider')]
    public function testAnyMethodWithoutSessionSetOnRequestThrowsException(\Closure $callback) : void {
        self::assertThrowsExceptionTypeWithMessage(
            $callback->bindTo($this, $this),
            SessionNotSetOnRequest::class,
            sprintf(
                'The %s Request attribute has not been set. Please set this value to an appropriate Session instance.',
                Session::class
            )
        );
    }

    public function testGetWithSessionSetOnRequestButKeyNotPresentReturnsNull() : void {
        $this->request->setAttribute(Session::class, $this->session);

        self::assertNull(
            $this->subject->get($this->request, SessionAttribute::UserId)
        );
    }

    public function testGetWithSessionSetOnRequestAndKeyIsPresentReturnsValue() : void {
        $this->request->setAttribute(Session::class, $this->session);
        $this->storage->write('known-session-id', [
            SessionAttribute::UserId->name => 'my-user-id'
        ]);

        self::assertSame(
            'my-user-id', $this->subject->get($this->request, SessionAttribute::UserId)
        );
    }

    public function testSetWithSessionSetOnRequestAddsCorrectValueToStorage() : void {
        $this->request->setAttribute(Session::class, $this->session);
        $this->session->lock();
        $this->subject->set($this->request, SessionAttribute::UserId, 'my-new-user-id');
        $this->session->commit();

        $actual = $this->storage->read('known-session-id')[SessionAttribute::UserId->name] ?? null;

        self::assertSame('my-new-user-id', $actual);
    }

    public function testUnsetWithSessionSetOnRequestRemovesValueFromStorage() : void {
        $this->storage->write('known-session-id', [
            SessionAttribute::UserId->name => 'my-user-id'
        ]);
        $this->request->setAttribute(Session::class, $this->session);

        self::assertTrue($this->session->has(SessionAttribute::UserId->name));

        $this->session->lock();
        $this->subject->unset($this->request, SessionAttribute::UserId);
        $this->session->commit();

        self::assertFalse($this->session->has(SessionAttribute::UserId->name));
    }

}