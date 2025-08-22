<?php

namespace App\Exports;

use App\Models\ContractStats;
use App\Services\Helpers\Check;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ContractStatsExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading
{
    use Exportable;

    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        ini_set('memory_limit', '512M');
        $filters = (object) $this->filters;
        $partner_id   = auth()->user()->partner_id;
        if (Check::isAdmin() && isset($this->filters['partner_id'])) {
            $partner_id = (int)$this->filters['partner_id'];
        }
        $query = ContractStats::searchEngine();
        if ($partner_id > 0) {
            $query->where('contract_stats.partner_id', $partner_id);
        }

        $query = $query->when(isset($filters->cards_count), function ($query) use ($filters) {
            if ($filters->cards_count == 0) {
                $query->where('sv_cards', 0)
                    ->where('humo_cards', 0);
            } else {
                $query->where(($filters->cards_count == 1 ? 'sv_cards' : 'humo_cards'), 0);
            }
        })
        ->when(isset($filters->request_count), function ($query) use ($filters) {
            if ($filters->request_count == 0) {
                $query->where('sv_requests', 0)
                    ->where('humo_requests', 0);
            } else {
                $query->where(($filters->request_count == 1 ? 'sv_requests' : 'humo_requests'), 0);
            }
        })
        ->leftJoin('partners', 'partners.id', '=', 'contract_stats.partner_id')
        ->leftJoin('merchants', 'merchants.id', '=', 'contract_stats.merchant_id')
        ->select('contract_stats.*', 'partners.name as partner_name', 'merchants.name as merchant_name')
        ->orderByDesc('contract_stats.id');
        return $query;
    }

    public function headings(): array
    {
        return [
            'Пинфл',
            'Мерчант',
            'Кол-карты uzcard',
            'Кол-карты humo',
            'Кол-запросов uzcard',
            'Кол-запросов humo',
            'Последнее обновление uzcard',
            'Последнее обновление humo',
            'Дата обновлено',
        ];
    }

    public function map($contractStats): array
    {
        return [
            $contractStats->pinfl,
            $contractStats->merchant_name,
            "$contractStats->sv_cards",
            "$contractStats->humo_cards",
            "$contractStats->sv_requests",
            "$contractStats->humo_requests",
            $contractStats->latest_sv_request,
            $contractStats->latest_humo_request,
            $contractStats->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
