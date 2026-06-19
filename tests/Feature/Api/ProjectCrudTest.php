<?php

namespace Tests\Feature\Api;

class ProjectCrudTest extends ApiTestCase
{
    public function test_user_can_create_list_update_and_delete_projects(): void
    {
        $user = $this->createOrganizationUser();

        $createResponse = $this->actingAsApiUser($user)->postJson('/api/projects', [
            'name' => 'Website Redesign',
            'description' => 'Q2 initiative',
        ]);

        $createResponse->assertCreated()
            ->assertJsonPath('data.name', 'Website Redesign');

        $projectId = $createResponse->json('data.id');

        $this->actingAsApiUser($user)->getJson('/api/projects')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->actingAsApiUser($user)->putJson('/api/projects/'.$projectId, [
            'name' => 'Website Refresh',
        ])->assertOk()
            ->assertJsonPath('data.name', 'Website Refresh');

        $this->actingAsApiUser($user)->deleteJson('/api/projects/'.$projectId)
            ->assertOk();

        $this->assertSoftDeleted('projects', ['id' => $projectId]);
    }
}
