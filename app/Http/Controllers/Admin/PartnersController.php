<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\Default\TelegramSendMessage;
use App\Models\Contract;
use App\Models\ContractStats;
use App\Models\Partner;
use App\Models\PartnerStat;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\TransactionRequest;
use App\Models\User;
use App\Models\UserActivity;
use App\Services\Helpers\Check;
use App\Services\Helpers\Helper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PartnersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Check::permission('Просмотр партнера');
        $partners = Partner::searchEngine()
            ->orderByDesc('id')
            ->paginate(25);

        $roles = Role::all();

        return view('pages.partners.index', compact('partners', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Check::permission('Создать партнера');
        return view('pages.partners.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Check::permission('Создать партнера');
        $request->validate([
            'name' => 'required|string|max:255',
            'inn' => 'required|numeric|unique:partners',
            'phone' => 'required|string',
            'commission' => 'required|numeric|min:0',
            'is_active' => 'boolean|required',
            'auto' => 'boolean|required',
        ]);

        // Rewrite phone number
        $request->merge(['phone' => preg_replace('/[^0-9]/', '', $request->phone)]);
        Partner::create(array_merge($request->all(), ['created_by' => auth()->id()]));

        return redirect()->route('partners.index')->with('success', 'Партнер успешно создан!');
    }

    //Configurations update
    public function configurations(Request $request, $id)
    {
        Check::permission('Редактировать партнера');
        $partner = Partner::findOrFail($id);
        $request->validate([
            'config' => 'required|array',
            'config.sms.host' => 'url|nullable',
            'config.webhook.host' => 'url|nullable',
        ]);
        $config = $request->get('config');
        $pConfig = $partner->config;
        $data = [
            'auth' => [
                'username' => $config['auth']['username'] ?? $pConfig['auth']['username'],
                'password' => $config['auth']['password'] ?? $pConfig['auth']['password'],
                'token' => $config['auth']['token'] ?? $pConfig['auth']['token'],
                'rate_limit' => $config['auth']['rate_limit'] ?? $pConfig['auth']['rate_limit'] ?? null,
            ],
            'webhook' => [
                'host' => $config['webhook']['host'] ?? $pConfig['webhook']['host'],
                'token' => $config['webhook']['token'] ?? $pConfig['webhook']['token'],
                'status' => $config['webhook']['status'] ?? $pConfig['webhook']['status']
            ],
            'card_service' => [
                'contract_stats' => $config['card_service']['contract_stats'] ?? $pConfig['card_service']['contract_stats'],
                'contracts' => $config['card_service']['contracts'] ?? $pConfig['card_service']['contracts'],
                'contract' => $config['card_service']['contract'] ?? $pConfig['card_service']['contract']
            ],
            'e_gov' => [
                'service' => $config['e_gov']['service'] ?? $pConfig['e_gov']['service']
            ],
            'flex' => [
                'host' => $config['flex']['host'] ?? ($pConfig['flex']['host'] ?? null),
                'token' => $config['flex']['token'] ?? ($pConfig['flex']['token'] ?? null),
                'status' => $config['flex']['status'] ?? ($pConfig['flex']['status'] ?? false),
            ],
            'sms' => [
                'username' => $config['sms']['username'] ?? ($pConfig['sms']['username'] ?? null),
                'password' => $config['sms']['password'] ?? ($pConfig['sms']['password'] ?? null),
                'status' => $config['sms']['status'] ?? $pConfig['sms']['status'],
            ],
            'verify_transaction' => [
                'host' => $config['verify_transaction']['host'] ?? ($pConfig['verify_transaction']['host'] ?? null),
                'token' => $config['verify_transaction']['token'] ?? ($pConfig['verify_transaction']['token'] ?? null),
                'status' => $config['verify_transaction']['status'] ?? ($pConfig['verify_transaction']['status'] ?? false),
            ],
            'notifications' => [
                'payment' => $config['notifications']['payment'] ?? $pConfig['notifications']['payment'],
                'warnings' => $config['notifications']['warnings'] ?? $pConfig['notifications']['warnings']
            ]
        ];
        $partner->update(['config' => $data]);
        return redirect()->back()
            ->with('success', "Конфигурация партнера {$partner->name} успешно обновлена!");
    }

    public function addUser(Request $request, $id)
    {
        Check::permission('Добавить пользователя в партнера');
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'roles' => 'nullable|array'
        ]);

        $partner = Partner::findOrFail($id);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = Helper::phoneFormatDB($request->phone);
        $user->password = Hash::make($request->password);
        $user->partner_id = $partner->id;
        $user->save();

        if (User::where('partner_id', $partner->id)->count() == 1) {
            $role = Role::firstOrCreate([
                'partner_id' => $partner->id,
                'name' => 'Super Admin',
                'guard_name' => 'web'
            ]);
            $user->assignRole($role);
        } else {
            $roles = Role::whereIN('id', $request->roles)->get();
            foreach ($roles as $role) {
                $user->assignRole($role);
            }
        }

        return redirect()->back()->with('success', "Пользователь {$user->name} успешно добавлен в партнера {$partner->name}!");
    }

    // Toggle Auto
    public function toggleAuto(Request $request)
    {
        Check::permission('Редактировать партнера');

        $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'auto' => 'required|boolean'
        ]);
        $partner = Partner::findOrFail($request->partner_id);
        try {
            UserActivity::create([
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'description' => "Изменение статуса автосписания",
                'action' => "AutoToggle",
                'data' => [
                    'new_auto' => $request->auto,
                    'old_auto' => $partner->auto,
                ],
                'model' => Partner::class,
                'model_id' => 0,
                'partner_id' => $request->partner_id
            ]);
        } catch (\Exception $exception) {
            Helper::exceptionSend($exception);
        }

        $partner->update(['auto' => $request->auto]);
        return redirect()->back()
            ->with('success', "Статус автосписания для партнера {$partner->name} успешно изменено!");
    }

    public function toggleStatus(Request $request)
    {
        Check::permission('Редактировать партнера');
        $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'is_active' => 'required|boolean'
        ]);
        $partner = Partner::findOrFail($request->partner_id);
        try {
            UserActivity::create([
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'description' => "Изменение статуса партнера",
                'action' => "PartnerStatus",
                'data' => [
                    'new_status' => $request->is_active,
                    'old_status' => $partner->is_active,
                ],
                'model' => Partner::class,
                'model_id' => 0,
                'partner_id' => $request->partner_id
            ]);
        } catch (\Exception $exception) {
            Helper::exceptionSend($exception);
        }
        $partner->update(['is_active' => $request->is_active]);
        return redirect()->back()
            ->with('success', "Статус партнера {$partner->name} успешно изменено!");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        Check::permission('Просмотр кабинета партнера');

        $partner = Partner::withCount(['merchants', 'clients'])
            ->findOrFail($id);

        $contracts = Contract::selectRaw('SUM(current_debt) as current_debt, COUNT(*) as count')
            ->where('partner_id', $partner->id)
            ->where('auto', true)
            ->where('current_debt', '>', 0)
            ->first();
        $statCurrent = PartnerStat::where('partner_id', $partner->id)
            ->where('stat_month', date('Y-m'))
            ->first();

        $statPrev = PartnerStat::where('partner_id', $partner->id)
            ->where('stat_month', date('Y-m', strtotime('-1 month')))
            ->first();

        $users = User::where('partner_id', $partner->id)->get();

        $partnerStats = PartnerStat::where('partner_id', $partner->id)
            ->orderByDesc('stat_month')
            ->limit(9)
            ->get();

        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

        $transactionsByType = DB::table('transactions')
            ->selectRaw("
                CASE WHEN created_by IS NULL THEN 'system' ELSE 'user' END as type,
                SUM(amount) as total_amount,
                COUNT(*) as total_count
            ")
            ->where('partner_id', $partner->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->groupBy('type')
            ->get();

        return view('pages.partners.show', compact('partner', 'users', 'statCurrent', 'statPrev', 'contracts', 'partnerStats', 'transactionsByType'));
    }

    public function filterStats(Request $request, string $id)
    {
        $request->validate([
            'date' => 'nullable|date|max:100',
            'date_operator' => 'required|string|in:=,>,<,between',
            'date_pair' => 'required_if:date_operator,between|date|max:100|nullable',
        ]);

        $operator = $request->input('date_operator');
        $date = $request->input('date');
        $datePair = $request->input('date_pair');

        if (!$operator || !$date) return response()->json('', 422);

        $query = DB::table('transactions')
            ->selectRaw("
                CASE WHEN created_by IS NULL THEN 'system' ELSE 'user' END as type,
                SUM(amount) as total_amount,
                COUNT(*) as total_count
            ")->where('partner_id', $id);

        switch ($operator) {
            case '=':
            case '>':
            case '<':
                $query->where('date', $operator, $date);
                break;
            case 'between':
                if ($datePair) {
                    $query->whereBetween('date', [$date, $datePair]);
                }
                break;
        }

        $results = $query
            ->groupBy('type')
            ->get()
            ->map(function ($item) {
                $item->total_amount = number_format($item->total_amount / 100, 2, '.', ',') ?? '0.00';
                $item->total_count = number_format($item->total_count) ?? '0';
                return $item;
            });
        if ($results->isEmpty()) return response()->json('', 422);
        return response()->json($results);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        Check::permission('Редактировать партнера');
        $partner = Partner::findOrFail($id);
        return view('pages.partners.edit', compact('partner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Check::permission('Редактировать партнера');
        $partner = Partner::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'inn' => 'required|numeric|unique:partners,inn,' . $partner->id,
            'phone' => 'required|string',
            'commission' => 'required|numeric|min:0',
            'commission_humo' => 'required|numeric|min:0',
            'is_active' => 'boolean|required',
            'auto' => 'boolean|required',
        ]);
        $partner->update($request->all());
        return redirect()->route('partners.index')->with('success', 'Партнер успешно обновлен!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Check::permission('Удалить партнера');
    }

    public function search(Request $request)
    {
        Check::permission('Просмотр партнера');
        $partners = Partner::whereRaw("name ILIKE ?", ["%$request->search%"])
            ->select('id', 'name')
            ->orderBy('id', 'desc')
            ->get();
        return response()->json($partners);
    }
}
