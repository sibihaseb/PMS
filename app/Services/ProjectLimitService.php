<?php

namespace App\Services;

use App\Enums\Plan;
use App\Exceptions\ProjectLimitExceededException;
use App\Models\Organization;

class ProjectLimitService
{
    public function limitFor(Organization $organization): ?int
    {
        return $organization->currentPlan()->projectLimit();
    }

    public function usageFor(Organization $organization): int
    {
        return $organization->projectsCount();
    }

    public function assertCanCreate(Organization $organization): void
    {
        if (! $organization->canCreateProject()) {
            throw new ProjectLimitExceededException;
        }
    }

    /**
     * @return array{plan: string, projects_used: int, projects_limit: int|null}
     */
    public function statusFor(Organization $organization): array
    {
        $plan = $organization->currentPlan();

        return [
            'plan' => $plan->value,
            'projects_used' => $this->usageFor($organization),
            'projects_limit' => $plan === Plan::Pro ? null : $this->limitFor($organization),
        ];
    }
}
