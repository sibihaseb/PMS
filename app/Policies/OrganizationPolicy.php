<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
{
    public function manageBilling(User $user, Organization $organization): bool
    {
        return $user->organization_id === $organization->id && $user->isOwner();
    }
}
