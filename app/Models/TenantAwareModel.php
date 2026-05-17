<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

abstract class TenantAwareModel extends Model
{
    protected static function booted()
    {
        // Automatically scope all queries to current tenant
        static::addGlobalScope('tenant', function (Builder $builder) {
            $companyId = request()?->attributes->get('company_id') ?? auth()?->user()?->company_id;
            
            if ($companyId) {
                $builder->where('company_id', $companyId);
            }
        });

        // Prevent accidental cross-tenant updates
        static::updating(function ($model) {
            if (request()?->user() && $model->company_id !== request()->user()->company_id) {
                throw new \Exception("Unauthorized: Cannot update record from another tenant");
            }
        });

        // Prevent accidental cross-tenant deletes
        static::deleting(function ($model) {
            if (request()?->user() && $model->company_id !== request()->user()->company_id) {
                throw new \Exception("Unauthorized: Cannot delete record from another tenant");
            }
        });
    }

    public function scopeForCompany(Builder $query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeExcludeTenant(Builder $query)
    {
        return $query->withoutGlobalScope('tenant');
    }
}
