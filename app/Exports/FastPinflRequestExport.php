<?php

namespace App\Exports;

use App\Models\PinflRequest;
use App\Services\Helpers\Check;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FastPinflRequestExport
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

        $createdAt = $this->filters['created_at'] ?? null;
        $createdAtPair = $this->filters['created_at_pair'] ?? null;
        $createdAtOp = $this->filters['created_at_operator'] ?? '=';

        if (!empty($createdAt)) {
            $parsedFrom = Carbon::parse($createdAt);

            if ($createdAtOp === 'between' && !empty($createdAtPair)) {
                $parsedTo = Carbon::parse($createdAtPair);

                $query->whereBetween('pinfl_requests.created_at', [$parsedFrom->startOfDay(), $parsedTo->endOfDay()]);
            } else {
                $query->whereDate('pinfl_requests.created_at', $createdAtOp, $parsedFrom->format('Y-m-d H:i:s'));
            }
        }

        $query->leftJoin('users', 'pinfl_requests.created_by', '=', 'users.id')
            ->leftJoin('partners', 'pinfl_requests.partner_id', '=', 'partners.id')
            ->select(
                'pinfl_requests.*',
                'users.name as creator_name',
                'partners.name as partner_name'
            )
            ->orderBy('pinfl_requests.id', 'desc');

        return $query;
    }

    public function headings(): array
    {
        return [
            'Партнер',
            'ПИНФЛ',
            'Владелец',
            'Тип',
            'Количество найденных карт',
            'Статус',
            'Дата создания',
            'Кем создан',
            'Последнее проведение',
            'Комментарии',
        ];
    }
    public function streamCsv()
    {
        $filename = "pinfl_requests_" . now()->format('Y-m-d_H-i-s');
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            // Add headers to CSV
            fputcsv($handle, $this->headings(), ";");

            // Use a cursor to keep memory usage low regardless of result size
            $query = $this->query();

            foreach ($query->cursor() as $transaction) {
                fputcsv($handle, [
                        $transaction->partner_name,
                        $transaction->pinfl,
                        $transaction->owner,
                        $transaction->type,
                        $transaction->cards_count,
                        $transaction->status,
                        $transaction->created_at,
                        $transaction->creator_name??'API',
                        $transaction->processed_at,
                        $transaction->comment,
                    ], ";" );
            }
            fclose($handle);
        };
        return new StreamedResponse($callback, 200, $headers);
    }
}
