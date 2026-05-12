<?php

use PHPUnit\Framework\TestCase;

require_once './Helpers/Auth.php';

class AuthenticationTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function test_user_is_not_authenticated_before_login()
    {
        $this->assertFalse(Auth::check());
    }

    public function test_user_becomes_authenticated_after_login()
    {
        Auth::login(15, 'abdelrahman', 'abdelrahman@test.com', 2);

        $this->assertTrue(Auth::check());
        $this->assertSame(15, $_SESSION['userid']);
        $this->assertSame('abdelrahman', $_SESSION['username']);
        $this->assertSame('abdelrahman@test.com', $_SESSION['email']);
        $this->assertSame(2, $_SESSION['user_roleid']);
    }

    public function test_user_is_not_authenticated_after_logout()
    {
        Auth::login(15, 'abdelrahman', 'abdelrahman@test.com', 2);
        Auth::logout();

        $this->assertFalse(Auth::check());
        $this->assertArrayNotHasKey('userid', $_SESSION);
        $this->assertArrayNotHasKey('user_roleid', $_SESSION);
    }
}
