<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;
use App\Exceptions\CreateObjectException;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'user-view-file-log']);
        Role::firstOrCreate(['name' => 'User'])->givePermissionTo('user-view-file-log');
    }

    public function it_creates_user_with_default_permissions_and_role()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password'
        ];

        $user = User::createUserWithDefaultPermissionsAndRole($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test User', $user->name);
        $this->assertTrue($user->hasRole('User'));
        $this->assertTrue($user->can('user-view-file-log'));
    }

    /** @test */
    public function it_throws_exception_when_validation_fails()
    {
        $this->expectException(CreateObjectException::class);

        $invalidData = [
            'name' => 'Incomplete User',
            'password' => 'password'
        ];

        User::createUserWithDefaultPermissionsAndRole($invalidData);
    }
}
