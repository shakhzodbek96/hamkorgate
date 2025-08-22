<?php

namespace App\Exports;

use App\Models\TransactionRequest;
use App\Services\Helpers\Check;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\Exportable;

class TransReqsExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading
{
    use Exportable;

    public function query()
    {
        ini_set('memory_limit', '512M');
        return TransactionRequest::searchEngine()
            ->leftJoin('partners', 'transaction_requests.partner_id', '=', 'partners.id')
            ->select('transaction_requests.*', 'partners.name as partner_name');
    }

    public function headings(): array
    {
        return [
            'Партнер',
            'PINFL',
            'РРН',
            'EXT',
            'Карта',
            'Сумма',
            'Статус',
            'Тип Процессинг',
            'Дата'
        ];
    }

    public function map($trReqs): array
    {
        $partnerName = $trReqs->partner_name;
        $pinfl = $trReqs->pinfl;
        $ext = $trReqs->ext ?? '';
        $rrn = $trReq->rrn ?? ($trReqs->processing === 'sv' ? data_get($trReqs->response, 'result.refNum', '') : data_get($trReqs->response, 'result.rrn', ''));

        if (Check::isAdmin()) {
            if ($trReqs->processing === 'sv') $cardId = data_get($trReqs->request, 'params.tran.cardId', '');
            else $cardId = data_get($trReqs->request, 'params.card_number', '');
        } else {
            if ($trReqs->processing === 'sv') $cardId = data_get($trReqs->response, 'result.pan', '');
            else {
                $raw = data_get($trReqs->request, 'params.card_number', '');
                $cardId = strlen($raw) >= 10 ? substr($raw, 0, 6) . str_repeat('*', 6) . substr($raw, -4) : $raw;
            }
        }

        $amount = number_format($trReqs->amount / 100, 2, '.') ?? '';
        $status = $trReqs->status ?? '';
        $processing = $trReqs->processing ?? '';
        $createdAt = $trReqs->created_at->format('Y-m-d H:i:s');

        return [
            $partnerName,
            $pinfl . ' ',
            $rrn . ' ',
            $ext,
            $cardId . ' ',
            $amount,
            $status,
            $processing,
            $createdAt,
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
