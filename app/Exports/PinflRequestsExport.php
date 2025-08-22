<?php

namespace App\Exports;

use App\Models\PinflRequest;
use App\Services\Helpers\Check;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class PinflRequestsExport implements FromQuery, WithMapping, WithHeadings, WithTitle, WithChunkReading
{
    protected array $filters;
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $partner_id = auth()->user()->partner_id;
        if (Check::isAdmin() && isset($this->filters['partner_id'])) $partner_id = (int)$this->filters['partner_id'];

        $query = PinflRequest::searchEngine();

        if ($partner_id > 0) $query->where('pinfl_requests.partner_id', $partner_id);

        $parsedDate = Carbon::createFromFormat('F Y', $this->filters['date']);
        $startOfMonth = $parsedDate->copy()->startOfMonth();
        $endOfMonth = $parsedDate->copy()->endOfMonth();
        $status = $this->filters['status'] ?? 'success';
        $amount = $this->filters['amount'] ?? null;
        $amountPair = $this->filters['amount_pair'] ?? null;
        $amountOp = $this->filters['amount_operator'] ?? '=';
        if (!empty($amount)) {
            $query->join(DB::raw('(SELECT pinfl, partner_id, SUM(amount) AS total_amount FROM transactions GROUP BY pinfl, partner_id) as trans'), function ($join) {
                $join->on('trans.pinfl', '=', 'pinfl_requests.pinfl')
                    ->on('trans.partner_id', '=', 'pinfl_requests.partner_id');
            });

            if ($amountOp === 'between' && !empty($amountPair)) {
                $query->whereBetween('trans.total_amount', [$amount, $amountPair]);
            } else {
                $query->where('trans.total_amount', $amountOp, $amount);
            }
        }

        $query->whereBetween('pinfl_requests.created_at', [$startOfMonth, $endOfMonth])
            ->where('pinfl_requests.status', $status)
            ->where('pinfl_requests.cards_count', '>', 0);

        return $query->leftJoin('partners', 'pinfl_requests.partner_id', '=', 'partners.id')
            ->groupBy('pinfl_requests.partner_id', 'partners.name')
            ->selectRaw('partners.name as partner_name, COUNT(pinfl_requests.id) as total_requests')
            ->orderBy('partners.name', 'asc');
    }

    public function map($row): array
    {
        return [
            $row->partner_name,
            $row->total_requests,
        ];
    }

    public function headings(): array
    {
        return [
            'Партнер',
            'Количество запросов',
        ];
    }


    public function title(): string
    {
        return 'cards_count_';
    }


    public function chunkSize(): int
    {
        return 1000;
    }
}
