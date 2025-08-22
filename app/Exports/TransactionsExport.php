<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class TransactionsExport implements FromCollection,WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data[] = [
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
        ];
        Transaction::searchEngine()
            ->withTrashed()
             ->leftJoin('merchants', 'transactions.merchant_id', '=', 'merchants.id')
                ->leftJoin('users', 'transactions.created_by', '=', 'users.id')
                ->leftJoin('users as reversers', 'transactions.cancelled_by', '=', 'reversers.id')
                ->leftJoin('clients', function($join) {
                    // Join on the raw concatenation expression:
                    $join->on('clients.uuid', '=', DB::raw("CONCAT(transactions.partner_id, '-', transactions.pinfl)"));
                })
                ->orderByDesc('id')
            ->chunk(2000, function ($transactions) use (&$data) {
                foreach ($transactions as $transaction) {
                    $data[] = [
                        $transaction->merchant_relation->name ?? 'without merchant',
                        $transaction->pinfl.' ',
                        $transaction->ext,
                        $transaction->loan_id,
                        isset($transaction->contract->client) ? $transaction->contract->client->fio() : 'Client deleted',
                        $transaction->card['pan'] ?? 'Card deleted',
                        $transaction->card['owner'] ?? 'Card deleted',
                        $transaction->amount / 100,
                        $transaction->date,
                        $transaction->rrn,
                        $transaction->status,
                        $transaction->merchant,
                        $transaction->terminal,
                        $transaction->created_at,
                        $transaction->reverser->name ?? '-',
                        $transaction->cancelled_at,
                    ];
                }
            });

        return collect($data);
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_NUMBER_00,
            'G' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'J' => NumberFormat::FORMAT_TEXT,
            'K' => NumberFormat::FORMAT_TEXT,
            'O' => NumberFormat::FORMAT_TEXT,
            'P' => NumberFormat::FORMAT_TEXT,
            'Q' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
