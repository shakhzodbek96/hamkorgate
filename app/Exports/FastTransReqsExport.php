<?php

namespace App\Exports;

use App\Models\TransactionRequest;
use App\Services\Helpers\Check;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FastTransReqsExport
{
    public function streamCsv()
    {
        $filename = "transaction_reqs_" . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Партнер',
                'PINFL',
                'РРН',
                'EXT',
                'Карта',
                'Сумма',
                'Статус',
                'Тип Процессинг',
                'Дата'
            ], ';');

            $query = TransactionRequest::searchEngine()
                ->leftJoin('partners', 'transaction_requests.partner_id', '=', 'partners.id')
                ->select('transaction_requests.*', 'partners.name as partner_name');

            $query->chunk(1000, function ($rows) use ($handle) {
                foreach ($rows as $trReq) {
                    $rrn = $trReq->rrn ?? ($trReq->processing === 'sv'
                        ? data_get($trReq->response, 'result.refNum', '--')
                        : data_get($trReq->response, 'result.rrn', '--')
                    );
                    $ext = $trReq->ext ?? '--';

                    if (Check::isAdmin()) {
                        $cardId = $trReq->processing === 'sv'
                            ? data_get($trReq->request, 'params.tran.cardId', '--')
                            : data_get($trReq->request, 'params.card_number', '--');
                    } else {
                        if ($trReq->processing === 'sv') {
                            $cardId = data_get($trReq->response, 'result.pan', '--');
                        } else {
                            $raw = data_get($trReq->request, 'params.card_number', '--');
                            $cardId = strlen($raw) >= 10
                                ? substr($raw, 0, 6) . str_repeat('*', 6) . substr($raw, -4)
                                : $raw;
                        }
                    }

                    $row = [
                        $trReq->partner_name,
                        $trReq->pinfl . ' ',
                        $rrn . ' ',
                        $ext,
                        "'$cardId'",
                        (float)$trReq->amount,
                        $trReq->status ?? '---',
                        $trReq->processing,
                        $trReq->created_at->format('Y-m-d H:i:s'),
                    ];
                    fputcsv($handle, $row, ';');
                }
            });
            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
