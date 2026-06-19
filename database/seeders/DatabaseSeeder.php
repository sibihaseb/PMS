<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::factory()->create([
            'name' => 'Demo Company',
        ]);

        User::factory()
            ->forOrganization($organization)
            ->owner()
            ->create([
                'name' => 'Demo Owner',
                'email' => 'demo@workboard.test',
            ]);

        Project::factory()
            ->count(2)
            ->create([
                'organization_id' => $organization->id,
            ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
