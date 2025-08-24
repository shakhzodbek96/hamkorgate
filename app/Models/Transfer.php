<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $fillable = [
        // General
        'partner_id', 'ext_id',

        // Receiver
        'receiver_account_type', 'receiver_account',

        // Debit
        'debit_amount', 'debit_currency', 'debit_description',
        'debit_commission', 'debit_form_url', 'debit_ref_num',

        // Credit
        'credit_stage', 'exchange_rate', 'credit_amount',
        'credit_currency', 'credit_ref_num',

        // Sender
        'sender_account', 'sender_name', 'sender_surname',
        'sender_series', 'sender_birth_date',
    ];

    protected $casts = [
        'debit_amount' => 'decimal:2',
        'debit_commission' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'credit_amount' => 'decimal:2',
        'sender_birth_date' => 'date',
    ];
}
