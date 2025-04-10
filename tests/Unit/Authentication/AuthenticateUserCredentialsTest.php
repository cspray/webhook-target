<?php declare(strict_types=1);

namespace Cspray\WebhookTarget\Tests\Unit\Authentication;

use Cspray\WebhookTarget\Authentication\AuthenticateUserCredentials;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AuthenticateUserCredentials::class)]
class AuthenticateUserCredentialsTest extends TestCase {

    private AuthenticateUserCredentials $subject;

    protected function setUp() : void {
        $this->subject = new AuthenticateUserCredentials(
            'username',
            password_hash('password', PASSWORD_DEFAULT)
        );
    }

    public function testUsernameDoesNotMatchConfiguredValueReturnsFalse() : void {
        self::assertFalse($this->subject->authenticate('wrong-user', 'password'));
    }

    public function testUserDoesMatchButPasswordIsNotCorrectReturnsFalse() : void {
        self::assertFalse($this->subject->authenticate('username', 'wrong-password'));
    }

    public function testUserDoesMatchWithGoodPasswordReturnsTrue() : void {
        self::assertTrue($this->subject->authenticate('username', 'password'));
    }

}