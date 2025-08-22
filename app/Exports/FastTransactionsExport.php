<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FastTransactionsExport
{
    public function streamCsv()
    {
        $filename = "transactions_" . now()->format('Y-m-d_H-i-s');
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
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
                'Филиал',
                'ПИНФЛ',
                'Внешный ИД',
                'Лоан ИД',
                'ФИО',
                'Номер карты',
                'Владелец карты',
                'Сумма транзакции',
                'Дата транзакции',
                'RRN',
                'Статус',
                'Мерчант',
                'Терминал',
                'Время_создания',
                'Отменено пользователем',
                'Отмененное время',
            ],";");

            // Use a cursor to keep memory usage low regardless of result size
            $query = Transaction::searchEngine()
                ->withTrashed()
                // Filter by PAN if provided
                ->when(\request()->filled('pan'), function ($query) {
                    return $query->where('card->pan', 'like', '%' . \request()->pan . '%');
                })
                // Filter by date range if provided
                ->when(\request()->filled('date_from'), function ($query) {
                    return $query->whereDate('transactions.created_at', '>=', \request()->date_from);
                })
                ->when(\request()->filled('date_to'), function ($query) {
                    return $query->whereDate('transactions.created_at', '<=', \request()->date_to);
                })
                // Filter by merchant if provided
                ->when(\request()->filled('merchant_id'), function ($query) {
                    return $query->where('transactions.merchant_id', \request()->merchant_id);
                })
                // Filter by status if provided
                ->when(\request()->filled('status'), function ($query) {
                    return $query->where('transactions.status', \request()->status);
                })
                // Add all the joins
                ->leftJoin('merchants', 'transactions.merchant_id', '=', 'merchants.id')
                ->leftJoin('users', 'transactions.created_by', '=', 'users.id')
                ->leftJoin('users as reversers', 'transactions.cancelled_by', '=', 'reversers.id')
                ->leftJoin('clients', function($join) {
                    // Join on the raw concatenation expression
                    $join->on('clients.uuid', '=', DB::raw("CONCAT(transactions.partner_id, '-', transactions.pinfl)"));
                })
                ->select(
                    'transactions.*',DB::raw('transactions.amount::numeric / 100 as sum'),
                    // Use raw expressions for concatenated fields
                    DB::raw("CONCAT(transactions.partner_id, '-', transactions.pinfl) as uuid"),
                    'merchants.name as merchant_name',
                    'users.name as creator_name',
                    'reversers.name as reverser_name',
                    DB::raw("CONCAT(COALESCE(clients.first_name, ''), ' ', COALESCE(clients.last_name, '')) as client_name")
                )
                ->orderBy('transactions.created_at');

            foreach ($query->cursor() as $transaction) {
                fputcsv($handle, [
                    $transaction->merchant_name ?? 'without merchant',
                    $transaction->pinfl.' ',
                    $transaction->ext,
                    $transaction->loan_id,
                    $transaction->client_name ?? '-----',
                    $transaction->card['pan'] ?? 'Card deleted',
                    $transaction->card['owner'] ?? 'Card deleted',
                    str_replace('.', ',', (float)$transaction->sum),
                    $transaction->date,
                    $transaction->rrn,
                    $transaction->status,
                    $transaction->merchant,
                    $transaction->terminal,
                    $transaction->created_at,
                    $transaction->reverser_name ?? '-',
                    $transaction->cancelled_at,
                ],";");
            }

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
