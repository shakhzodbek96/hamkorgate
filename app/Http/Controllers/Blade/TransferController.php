<?php

namespace App\Http\Controllers\Blade;

use App\Http\Controllers\Controller;
use App\Models\Transfer;

class TransferController extends Controller
{
    /**
     * Show list of transfers
     */
    public function index()
    {
        $transfers = Transfer::all();
        return view('pages.transfers.index', compact('transfers'));
    }

    /**
     * Show create transfer form
     */
    public function create()
    {
        return view('pages.transfers.create');
    }

    /**
     * Show transfer details
     */
    public function show($id)
    {
        return view('pages.transfers.show', compact('id'));
    }
}
