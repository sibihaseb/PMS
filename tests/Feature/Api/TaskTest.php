<?php

namespace Tests\Feature\Api;

use App\Models\Project;

class TaskTest extends ApiTestCase
{
    public function test_user_can_create_and_list_tasks_for_a_project(): void
    {
        $user = $this->createOrganizationUser();

        $project = Project::factory()->create([
            'organization_id' => $user->organization_id,
        ]);

        $createResponse = $this->actingAsApiUser($user)
            ->postJson('/api/projects/'.$project->id.'/tasks', [
                'title' => 'Write API docs',
            ]);

        $createResponse->assertCreated()
            ->assertJsonPath('data.title', 'Write API docs');

        $this->actingAsApiUser($user)
            ->getJson('/api/projects/'.$project->id.'/tasks')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Write API docs');
    }
}
