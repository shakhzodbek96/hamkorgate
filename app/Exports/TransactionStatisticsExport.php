<?php

namespace App\Exports;

use App\Services\Helpers\Check;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionStatisticsExport implements FromCollection, WithHeadings, WithTitle
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $partnerId = $this->filters['partner_id'] ?? null;
        $dateOperator = $this->filters['date_operator'] ?? '=';
        $date = $this->filters['date'] ?? Carbon::now()->format('d M, Y');
        $datePair = $this->filters['date_pair'] ?? null;

        $startDate = Carbon::createFromFormat('d M, Y', $date)->startOfDay()->toDateString();
        $endDate = $datePair ? Carbon::createFromFormat('d M, Y', $datePair)->endOfDay()->toDateString() : null;

        $partners = DB::table('partners')
            ->when(!Check::isAdmin(), fn($query) => $query->where('id', auth()->user()->partner_id))
            ->when($partnerId, fn($query) => $query->where('id', $partnerId))
            ->get()
            ->keyBy('id');

        $statsQuery = DB::table('transaction_statistics')
            ->when($partnerId, fn($query) => $query->where('partner_id', $partnerId))
            ->when(!Check::isAdmin(), fn($query) => $query->where('partner_id', auth()->user()->partner_id));

        switch ($dateOperator) {
            case '=':
                $statsQuery->where('stat_date', '=', $startDate);
                $groupBy = 'stat_date';
                $dateColumn = "DATE(stat_date) as stat_date";
                break;
            case 'between':
                $statsQuery->whereBetween('stat_date', [$startDate, $endDate]);
                $groupBy = null;
                $dateColumn = "TO_CHAR(MIN(stat_date), 'YYYY-MM') as stat_date";
                break;
            case '>':
            case '<':
                if ($dateOperator === '>') {
                    $statsQuery->where('stat_date', '>', $startDate);
                } else {
                    $statsQuery->where('stat_date', '<', $startDate);
                }
                $groupBy = null;
                $dateColumn = "'Общий итог' as stat_date";
                break;
            default:
                throw new \Exception('Invalid date operator');
        }

        $stats = $statsQuery->selectRaw("
                partner_id,
                SUM(total_amount) as total_amount,
                SUM(cancelled_amount) as cancelled_amount,
                SUM(sv_amount) as sv_amount,
                SUM(humo_amount) as humo_amount,
                {$dateColumn}
            ")
            ->groupBy('partner_id')
            ->when($groupBy, fn($query) => $query->groupBy($groupBy))
            ->orderBy('stat_date', 'desc')
            ->get();

        $data = [];

        foreach ($stats as $stat) {
            $partner = $partners->get($stat->partner_id);
            if ($partner) {
                $data[] = [
                    'partner' => $partner->name,
                    'partner_inn' => $partner->inn,
                    'date' => $dateOperator === 'between' ? "Месяц {$stat->stat_date}" : $stat->stat_date,
                    'total_amount' => round($stat->total_amount / 100),
                    'cancelled_amount' => round($stat->cancelled_amount / 100),
                    'sv_amount' => round($stat->sv_amount / 100),
                    'sv_commission' => $partner->commission,
                    'sv_transaction' => round(($partner->commission * ($stat->sv_amount / 100)) / 100),
                    'humo_amount' => round($stat->humo_amount / 100),
                    'humo_commission' => $partner->commission_humo,
                    'humo_transaction' => round(($partner->commission_humo * ($stat->humo_amount / 100)) / 100),
                ];
            }
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Партнер',
            'ИНН',
            'Дата',
            'Общая сумма',
            'Отмененная сумма',
            'Сумма SV',
            'Комиссия SV',
            'Транзакция SV',
            'Сумма Humo',
            'Комиссия Humo',
            'Транзакция Humo',
        ];
    }

    public function title(): string
    {
        return 'Отчет по транзакциям';
    }
}
