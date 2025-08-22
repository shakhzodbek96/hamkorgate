<?php

namespace App\Http\Controllers\Blade;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Services\Helpers\Check;

class PermissionsController extends Controller
{
    public function index()
    {
        Check::permission('Просмотр разрешений');
        if (Check::isAdmin())
            $permissions = Permission::with('roles')->orderByDesc('id')->paginate(25);
        else
            $permissions = Permission::where('guard_name','web')->with('roles')->orderByDesc('id')->paginate(25);

        return view('pages.permission.index',compact('permissions'));
    }
}
