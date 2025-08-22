<?php

namespace App\Exports;

use App\Models\Contract;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FastContractsExport
{
    public function streamCsv()
    {
        $filename = "contracts_" . now()->format('Y-m-d_H-i-s');
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function() {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            // Add headers to CSV
            fputcsv($handle, [
                'Пинфл', 'Лоан ID', 'Имя', 'Фамилия', 'Отчество', 'Мерчант',
                'Обшая задолженность', 'Текущий долг', 'Оплаченная сумма',
                'Внешний ID', 'Счет', 'Информация', 'Дата создания'
            ], ";");

            // Use a cursor to keep memory usage low regardless of result size
            $query = Contract::searchEngine()
                ->leftJoin('clients', 'contracts.uuid', '=', 'clients.uuid')
                ->leftJoin('merchants', 'contracts.merchant_id', '=', 'merchants.id')
                ->select([
                    'contracts.pinfl', 'contracts.loan_id',
                    'clients.first_name', 'clients.last_name', 'clients.middle_name',
                    'merchants.name as merchant',
                    DB::raw('contracts.total_debt::numeric /100 as total_debt'),
                    DB::raw('contracts.current_debt::numeric /100 as current_debt'),
                    DB::raw('contracts.paid_amount::numeric /100 as paid_amount'),
                    'contracts.ext', 'contracts.account', 'contracts.info',
                    'contracts.created_at'
                ])
                ->orderBy('contracts.id');

            $rowCount = 0;
            foreach ($query->cursor() as $contract) {
                fputcsv($handle, [
                    "'{$contract->pinfl}'",
                    $contract->loan_id,
                    $contract->first_name,
                    $contract->last_name,
                    $contract->middle_name,
                    $contract->merchant,
                    str_replace('.', ',', (float)$contract->total_debt),
                    str_replace('.', ',', (float)$contract->current_debt),
                    str_replace('.', ',', (float)$contract->paid_amount),
                    $contract->ext,
                    $contract->account,
                    $contract->info,
                    $contract->created_at instanceof \DateTime
                        ? $contract->created_at->format('Y-m-d H:i:s')
                        : $contract->created_at,
                ],';');
                if (++$rowCount % 10000 === 0) {
                    gc_collect_cycles();
                }
            }

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
