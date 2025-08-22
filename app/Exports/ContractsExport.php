<?php

namespace App\Exports;

use App\Models\Contract;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\DB;

class ContractsExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading, WithBatchInserts, ShouldAutoSize
{
    use Exportable;

    protected $batchSize = 500;
    protected $chunkSize = 500;

    public function __construct($batchSize = 1000, $chunkSize = 2000)
    {
        $this->batchSize = $batchSize;
        $this->chunkSize = $chunkSize;

        // Set higher memory limit and execution time for powerful server
        ini_set('memory_limit', '4G');
        set_time_limit(3600); // 60 minutes
    }

    public function query()
    {
        // Optimize the query by:
        // 1. Using select() to specify only needed columns
        // 2. Using proper indexing
        // 3. Utilizing searchEngine() as required
        return Contract::searchEngine()
            ->leftJoin('clients', 'contracts.uuid', '=', 'clients.uuid')
            ->leftJoin('merchants', 'contracts.merchant_id', '=', 'merchants.id')
            ->select([
                'contracts.pinfl',
                'contracts.loan_id',
                'clients.first_name',
                'clients.last_name',
                'clients.middle_name',
                'merchants.name as merchant',
                'contracts.total_debt',
                'contracts.current_debt',
                'contracts.paid_amount',
                'contracts.ext',
                'contracts.account',
                'contracts.info',
                'contracts.created_at'
            ])
            ->orderByDesc('contracts.id'); // Using orderByDesc as in the original code
    }

    public function headings(): array
    {
        return [
            'Пинфл',
            'Лоан ID',
            'Имя',
            'Фамилия',
            'Отчество',
            'Мерчант',
            'Обшая задолженность',
            'Текущий долг',
            'Оплаченная сумма',
            'Внешний ID',
            'Счет',
            'Информация',
            'Дата создания',
        ];
    }

    public function map($contract): array
    {
        return [
            "'{$contract->pinfl}'", // Fixed the quote placement
            $contract->loan_id,
            $contract->first_name,
            $contract->last_name,
            $contract->middle_name,
            $contract->merchant,
            (float)$contract->total_debt/100,
            (float)$contract->current_debt/100,
            (float)$contract->paid_amount/100,
            $contract->ext,
            $contract->account,
            $contract->info,
            $contract->created_at instanceof \DateTime
                ? $contract->created_at->format('Y-m-d H:i:s')
                : $contract->created_at,
        ];
    }

    public function batchSize(): int
    {
        return $this->batchSize;
    }

    public function chunkSize(): int
    {
        return $this->chunkSize;
    }
}
