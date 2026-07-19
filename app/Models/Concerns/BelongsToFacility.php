<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Stamps facility_id on create from the current RLS session context.
 *
 * This is a CONVENIENCE, not the security boundary. Row-Level Security is the
 * boundary: even if this trait is forgotten, or a query is written by hand, the
 * database refuses to return or accept another facility's rows (ADR-002).
 *
 * Deliberately no global scope. Adding `where facility_id = ...` in Eloquent on
 * top of RLS would duplicate the filter and, worse, invite the belief that the
 * application filter is what protects the data. It is not.
 */
trait BelongsToFacility
{
    protected static function bootBelongsToFacility(): void
    {
        static::creating(function ($model) {
            if ($model->facility_id === null) {
                $model->facility_id = static::currentFacilityId();
            }
        });
    }

    /**
     * The facility from the RLS session variable, set by SetFacilityContext
     * middleware from the AUTHENTICATED USER — never from a request header.
     */
    public static function currentFacilityId(): ?string
    {
        $row = DB::selectOne(
            "SELECT nullif(current_setting('app.facility_id', true), '') AS facility_id"
        );

        return $row?->facility_id;
    }

    /**
     * Escape hatch for background jobs and console commands, which have no
     * HTTP request and therefore no middleware to set the context.
     */
    public function scopeForFacility(Builder $query, string $facilityId): Builder
    {
        return $query->where($this->getTable().'.facility_id', $facilityId);
    }
}
