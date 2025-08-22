<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Models\Client;

class ClientsExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading
{
    use Exportable;

    public function query()
    {
        ini_set('memory_limit', '512M');
        return Client::searchEngine()->withCount('contracts');
    }

    public function headings(): array
    {
        // Define column headers
        return [
            'PINFL',
            'Passport',
            'First Name',
            'Last Name',
            'Middle Name',
            'Contract Count',
            'Partner ID',
            'Created At',
        ];
    }

    public function map($client): array
    {
        // Map each client row to the export columns
        return [
            $client->pinfl,
            $client->passport,
            $client->first_name,
            $client->last_name,
            $client->middle_name,
            $client->contracts_count,
            $client->partner_id,
            $client->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function chunkSize(): int
    {
        return 1000; // Adjust based on memory capacity; 1000 is a good balance for large exports
    }
}
