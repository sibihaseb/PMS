<?php

namespace App\Models;

use App\Enums\Plan;
use Database\Factories\OrganizationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Subscription;

class Organization extends Model
{
    /** @use HasFactory<OrganizationFactory> */
    use Billable, HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return HasMany<Project, $this>
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function currentPlan(): Plan
    {
        /** @var Subscription|null $subscription */
        $subscription = $this->subscription('default');

        if ($subscription === null || ! $subscription->active()) {
            return Plan::Free;
        }

        if ($subscription->stripe_price === config('cashier.team_price_id')) {
            return Plan::Team;
        }

        return Plan::Pro;
    }

    public function projectsCount(): int
    {
        return $this->projects()->count();
    }

    public function canCreateProject(): bool
    {
        $limit = $this->currentPlan()->projectLimit();

        if ($limit === null) {
            return true;
        }

        return $this->projectsCount() < $limit;
    }
}
