<?php

namespace App\Http\Controllers\Blade;

use App\Http\Controllers\Controller;

class TransferController extends Controller
{
    /**
     * Show list of transfers
     */
    public function index()
    {
        return view('pages.transfers.index');
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
