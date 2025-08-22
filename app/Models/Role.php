<?php

namespace App\Models;

use App\Traits\HasPartnerScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Searchable;

class Role extends Model
{
    use HasFactory,HasPartnerScope, Searchable;
    protected $fillable = ['partner_id','name', 'guard_name'];

    // Users that belong to the role
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    // Permissions that belong to the role
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    // Assign permission to role
    public function givePermissionTo($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->firstOrFail();
        }
        $this->permissions()->syncWithoutDetaching($permission);
    }

    // Remove permission from role
    public function revokePermissionTo($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->first();
        }
        $this->permissions()->detach($permission);
    }

    // Refresh permissions cache for users with this role
    public function refreshUsersCache()
    {
        foreach ($this->users as $user) {
            $user->clearPermissionsCache();
        }
    }

    public function hasPermissionTo()
    {

    }
}
