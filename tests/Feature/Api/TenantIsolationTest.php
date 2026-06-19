<?php

namespace Tests\Feature\Api;

use App\Models\Project;

class TenantIsolationTest extends ApiTestCase
{
    public function test_user_cannot_view_project_from_another_organization(): void
    {
        $orgAUser = $this->createOrganizationUser();
        $orgBUser = $this->createOrganizationUser();

        $project = Project::factory()->create([
            'organization_id' => $orgAUser->organization_id,
        ]);

        $this->actingAsApiUser($orgBUser)
            ->getJson('/api/projects/'.$project->id)
            ->assertNotFound();
    }

    public function test_user_cannot_update_project_from_another_organization(): void
    {
        $orgAUser = $this->createOrganizationUser();
        $orgBUser = $this->createOrganizationUser();

        $project = Project::factory()->create([
            'organization_id' => $orgAUser->organization_id,
        ]);

        $this->actingAsApiUser($orgBUser)
            ->putJson('/api/projects/'.$project->id, ['name' => 'Hacked'])
            ->assertNotFound();
    }

    public function test_user_cannot_delete_project_from_another_organization(): void
    {
        $orgAUser = $this->createOrganizationUser();
        $orgBUser = $this->createOrganizationUser();

        $project = Project::factory()->create([
            'organization_id' => $orgAUser->organization_id,
        ]);

        $this->actingAsApiUser($orgBUser)
            ->deleteJson('/api/projects/'.$project->id)
            ->assertNotFound();
    }

    public function test_user_only_sees_projects_from_their_organization(): void
    {
        $orgAUser = $this->createOrganizationUser();
        $orgBUser = $this->createOrganizationUser();

        Project::factory()->count(2)->create([
            'organization_id' => $orgAUser->organization_id,
        ]);

        Project::factory()->create([
            'organization_id' => $orgBUser->organization_id,
        ]);

        $response = $this->actingAsApiUser($orgAUser)->getJson('/api/projects');

        $response->assertOk();
        $this->assertCount(2, $response->json('data'));
    }
}
