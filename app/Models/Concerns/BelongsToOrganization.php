<?php

namespace App\Models\Concerns;

use App\Models\Organization;
use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToOrganization
{
    protected static function bootBelongsToOrganization(): void
    {
        static::addGlobalScope('organization', function (Builder $builder): void {
            if (TenantContext::get() !== null) {
                $builder->where(
                    $builder->getModel()->getTable().'.organization_id',
                    TenantContext::get()
                );
            }
        });

        static::creating(function (Model $model): void {
            if ($model->organization_id === null && TenantContext::get() !== null) {
                $model->organization_id = TenantContext::get();
            }
        });
    }

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
