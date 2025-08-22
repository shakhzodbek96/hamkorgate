<?php

namespace App\Http\Controllers\Blade;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\Role;
use App\Models\User;
use App\Models\UserActivity;
use App\Services\Helpers\Check;
use App\Services\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Sentry;

class UserController extends Controller
{
    public function index()
    {
        Check::permission('Просмотр пользователей');
        $users = User::searchEngine()
            ->orderByDesc('id')
            ->where('id', '!=', auth()->id())
            ->with('roles')
            ->with('partner')
            ->paginate();
        return view('pages.users.index', compact('users'));
    }

    public function create()
    {
        Check::permission('Создать пользователя');
        $roles = Check::isAdmin() ? Role::where('partner_id', 0)->get() : Role::all();
        $partners = Partner::select('id', 'name')
            ->when(!Check::isAdmin(), function ($query) {
                return $query->where('id', auth()->user()->partner_id);
            })
            ->get();
        return view('pages.users.create', compact('roles', 'partners'));
    }

    public function store(Request $request)
    {
        Check::permission('Создать пользователя');
        $request->merge(['phone' => Helper::phoneFormatDB($request->get('phone'))]);

        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6'],
            'is_admin' => ['nullable'],
        ]);

        $user = new User();
        $user->fill($request->all());
        $user->phone = Helper::phoneFormatDB($request->get('phone'));
        $user->password = Hash::make($request->password);

        if (!Check::isAdmin())
            $user->partner_id = auth()->user()->partner_id;

        if ($request->filled('is_admin') && Check::isAdmin()) {
            $user->is_admin = true;
            $user->partner_id = 0;
        }

        $user->save();
        if ($request->has('partner_id')) {
            if (User::where('partner_id', $request->partner_id)->count() == 1) {
                $role = Role::firstOrCreate([
                    'partner_id' => $request->partner_id,
                    'name' => 'Super Admin',
                    'guard_name' => 'web'
                ]);
                $user->assignRole($role);
            }
        } elseif (auth()->user()->hasPermission('Установить роль для пользователя') && $request->has('roles')) {
            $roles = Role::whereIN('id', $request->roles)->get();
            foreach ($roles as $role) {
                $user->assignRole($role);
            }
        }
        return redirect()->route('users.index')->with('success', "Создан новый пользователь с именем $user->name!");
    }

    public function edit($id)
    {
        if (auth()->id() != $id)
            Check::permission('Редактировать пользователя');

        $partners = Partner::select('id', 'name')
            ->when(!Check::isAdmin(), function ($query) {
                return $query->where('id', auth()->user()->partner_id);
            })
            ->get();

        $user = User::whereId($id)->with('roles')->firstOrFail();
        $roles = auth()->user()->hasPermission('Установить роль для пользователя')
            ? Role::where('partner_id', $user->partner_id ?? 0)->get() : [];
        $user->roles = array_flip($user->roles->map(function ($role) {
            return $role->name;
        })->toArray());

        return view('pages.users.edit', compact('user', 'roles', 'partners'));
    }

    public function update(Request $request, User $user)
    {
        if ($request->has('phone')) {
            $request->merge(['phone' => Helper::phoneFormatDB($request->get('phone'))]);
        }

        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', "unique:users,email,$user->id"],
            'phone' => ['required', 'min:9', 'string'],
            'password' => ['string', 'nullable', 'min:6'],
        ]);

        try {
            $user->fill($request->all());

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            if (auth()->user()->hasPermission("Установить роль для пользователя")) {
                if (isset($request->roles))
                    $user->roles()->sync($request->roles);
                else
                    $user->roles()->detach();
            }
            if (auth()->id() == $user->id)
                return redirect()->back()->with('success', "Ваш профиль успешно обновлен!");

            return redirect()->route('users.index')->with('success', "Пользователь $user->name успешно обновлен!");
        } catch (\Exception $e) {
            Sentry\captureException($e);
            return redirect()->back()->with('error', "Ошибка обновления пользователя $user->name!");
        }
    }

    public function destroy(User $user)
    {
        Check::permission('Удалить пользователя');
        try {
            if (Check::isAdmin())
                $user->forceDelete();
            else
                $user->delete();

            UserActivity::create([
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'description' => "Удален пользователь $user->name",
                'action' => UserActivity::DELETE,
                'data' => $user->toArray(),
                'model' => User::class,
                'model_id' => $user->id,
                'partner_id' => $user->partner_id
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Ошибка удаления пользователя $user->name!");
        }
        return redirect()->back()->with('success', "Пользователь $user->name успешно удален!");
    }
}
