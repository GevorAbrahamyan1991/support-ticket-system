<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function test_is_agent_returns_true_for_agent_role()
    {
        $user = new User(['role' => 'agent']);
        $this->assertTrue($user->isAgent());
        $this->assertFalse($user->isCustomer());
    }

    public function test_is_customer_returns_true_for_customer_role()
    {
        $user = new User(['role' => 'customer']);
        $this->assertTrue($user->isCustomer());
        $this->assertFalse($user->isAgent());
    }
} 