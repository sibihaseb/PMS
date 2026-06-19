<?php

namespace App\Models;

use App\Enums\Plan;
use Database\Factories\OrganizationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Cashier\Billable;

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
        if ($this->subscribed('default')) {
            return Plan::Pro;
        }

        return Plan::Free;
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
