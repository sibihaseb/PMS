<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;

class AuthTest extends ApiTestCase
{
    public function test_register_creates_organization_owner_and_token(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Jane Owner',
            'email' => 'jane@acme.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'organization_name' => 'Acme Corp',
        ]);

        $response->assertCreated()
            ->assertJsonPath('user.name', 'Jane Owner')
            ->assertJsonPath('user.email', 'jane@acme.test')
            ->assertJsonPath('user.role', UserRole::Owner->value)
            ->assertJsonPath('user.organization.name', 'Acme Corp')
            ->assertJsonStructure(['token']);

        $this->assertDatabaseHas('organizations', ['name' => 'Acme Corp']);
        $this->assertDatabaseHas('users', [
            'email' => 'jane@acme.test',
            'role' => UserRole::Owner->value,
        ]);
    }

    public function test_login_returns_token(): void
    {
        $user = $this->createOrganizationUser();

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonPath('user.email', $user->email)
            ->assertJsonStructure(['token']);
    }

    public function test_me_returns_user_with_organization(): void
    {
        $user = $this->createOrganizationUser();

        $response = $this->actingAsApiUser($user)->getJson('/api/me');

        $response->assertOk()
            ->assertJsonPath('data.email', $user->email)
            ->assertJsonPath('data.organization.name', $user->organization->name);
    }

    public function test_logout_revokes_token(): void
    {
        $user = $this->createOrganizationUser();
        $token = $user->createToken('api')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/logout');

        $response->assertOk();
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_unauthenticated_me_returns_unauthorized(): void
    {
        $this->getJson('/api/me')->assertUnauthorized();
    }
}
