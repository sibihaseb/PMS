<?php

namespace Tests\Feature\Api;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

abstract class ApiTestCase extends TestCase
{
    use RefreshDatabase;

    protected function createOrganizationUser(bool $asOwner = true): User
    {
        $organization = Organization::factory()->create();

        return User::factory()
            ->forOrganization($organization)
            ->{$asOwner ? 'owner' : 'member'}()
            ->create();
    }

    protected function actingAsApiUser(User $user): static
    {
        Sanctum::actingAs($user);

        return $this;
    }
}
