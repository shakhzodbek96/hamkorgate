<?php

namespace App\Http\Controllers\Blade;

use App\Http\Controllers\Controller;
use App\Services\Helpers\Check;
use App\Services\Helpers\CompareOwners;
use Illuminate\Http\Request;

class CompareFioController extends Controller
{
    public function index()
    {
        if (!Check::isAdmin()){
            abort(404);
        }
        return view('pages.clients.compare');
    }

    public function compare(Request $request)
    {
        $fio1 = $request->input('fio1');
        $fio2 = $request->input('fio2');

        if (!$fio1 || !$fio2) {
            return response()->json(['error' => 'Не все поля заполнены'], 422);
        }

        $match = CompareOwners::compareNames($fio1, $fio2);

        $percent = max(0, min(100, $match));

        return response()->json(['similarity' => $percent]);
    }
}
