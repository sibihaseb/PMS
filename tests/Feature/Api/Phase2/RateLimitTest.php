<?php

namespace Tests\Feature\Api\Phase2;

use Tests\Feature\Api\ApiTestCase;

class RateLimitTest extends ApiTestCase
{
    public function test_organization_is_rate_limited_to_sixty_requests_per_minute(): void
    {
        $user = $this->createOrganizationUser();

        $this->actingAsApiUser($user);

        for ($i = 0; $i < 60; $i++) {
            $this->getJson('/api/me')->assertOk();
        }

        $this->getJson('/api/me')->assertStatus(429);
    }
}
