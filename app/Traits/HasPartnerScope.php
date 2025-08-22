<?php

namespace App\Traits;

use App\Models\Scopes\PartnerScope;

trait HasPartnerScope
{
    public static function bootHasPartnerScope()
    {
        static::addGlobalScope(new PartnerScope());
    }
}
