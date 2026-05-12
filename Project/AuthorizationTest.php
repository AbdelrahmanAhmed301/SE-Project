<?php

use PHPUnit\Framework\TestCase;

require_once './Helpers/Auth.php';

class AuthorizationTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function test_guest_has_no_role_access()
    {
        $this->assertFalse(Auth::role(1));
        $this->assertFalse(Auth::role(2));
        $this->assertFalse(Auth::role(3));
    }

    public function test_admin_has_admin_authorization_only()
    {
        Auth::login(1, 'admin', 'admin@test.com', 1);

        $this->assertTrue(Auth::isAdmin());
        $this->assertFalse(Auth::isClient());
        $this->assertFalse(Auth::isFreelancer());
    }

    public function test_client_has_client_authorization_only()
    {
        Auth::login(2, 'client', 'client@test.com', 2);

        $this->assertTrue(Auth::isClient());
        $this->assertFalse(Auth::isAdmin());
        $this->assertFalse(Auth::isFreelancer());
    }

    public function test_freelancer_has_freelancer_authorization_only()
    {
        Auth::login(3, 'freelancer', 'freelancer@test.com', 3);

        $this->assertTrue(Auth::isFreelancer());
        $this->assertFalse(Auth::isAdmin());
        $this->assertFalse(Auth::isClient());
    }
}
