<?php

namespace App\Exports;

use App\Models\Client;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FastClientsExport
{
    public function streamCsv()
    {
        $filename = "clients_" . now()->format('Y-m-d_H-i-s');
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
                'Пинфл',
                'Паспорт',
                'Имя',
                'Фамилия',
                'Отчество',
                'Дата создания'
            ],";");

            $query = Client::searchEngine();

            foreach ($query->cursor() as $client) {
                fputcsv($handle, [
                    $client->pinfl.' ',
                    $client->passport,
                    $client->first_name,
                    $client->last_name,
                    $client->middle_name,
                    $client->created_at->format('Y-m-d H:i:s'),
                ],";");
            }

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
