<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Session;

class PartnerScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model)
    {
        // Check if there is a logged-in user
        if (Session::has('partner_id') && (Session::has('is_admin') && Session::get('is_admin') == 0)) {
            $builder->where($model->getTable() . '.partner_id', Session::get('partner_id'));
        }

        // Check if the partner ID is set in the current request (for API users) and partner is instance of Partner Model
        if (request()->has('partner')) {
            $partnerId = request()->get('partner')['id'] ?? 0;
            $builder->where($model->getTable() . '.partner_id', $partnerId);
        }
    }
}
