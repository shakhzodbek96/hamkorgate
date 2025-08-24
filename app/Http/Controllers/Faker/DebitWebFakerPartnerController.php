<?php

namespace App\Http\Controllers\Faker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Mts\MultitransferServiceFaker as Svc;

class DebitWebFakerPartnerController extends Controller
{
    public function __construct(private Svc $svc) {}

    // GET /pay/{ext_id}
    public function showForm(string $ext_id)
    {
        $p = $this->svc->get($ext_id);
        if (!$p) abort(404);

        if ($p['state'] === Svc::CREATED) {
            $p['state'] = Svc::FORM_OPENED;
            $this->svc->set($ext_id, $p);
        }

        return view('pages.fake_debit.form', ['p' => $p]);
    }

    // POST /pay/{ext_id}
    public function submitForm(Request $request, string $ext_id)
    {
        $request->validate([
            'card_number' => 'required|string|min:12|max:19',
            'expire'      => 'required|string', // MM/YY
            'cvv'         => 'required|string|min:3|max:4',
        ]);

        $p = $this->svc->get($ext_id);
        if (!$p) abort(404);

        $pan = preg_replace('/\D+/', '', $request->card_number);
        $masked = substr($pan, 0, 6) . '****' . substr($pan, -4);

        $p['card']  = ['masked' => $masked, 'exp' => $request->expire];
        $p['state'] = Svc::REQ_3DS;
        $this->svc->set($ext_id, $p);

        return redirect()->route('pay.3ds', ['ext_id' => $ext_id]);
    }

    // GET /3ds/{ext_id}
    public function show3DS(string $ext_id)
    {
        $p = $this->svc->get($ext_id);
        if (!$p) abort(404);
        if ($p['state'] < Svc::REQ_3DS) {
            return redirect()->route('pay.form', ['ext_id' => $ext_id]);
        }
        return view('pages.fake_debit.3ds', ['p' => $p]);
    }

    // POST /3ds/{ext_id}
    public function submit3DS(Request $request, string $ext_id)
    {
        $request->validate(['otp' => 'required|string|min:4|max:8']);

        $p = $this->svc->get($ext_id);
        if (!$p) abort(404);

        // Any OTP -> success (you can add failure rules)
        $p['state'] = Svc::PROCESSING;
        $this->svc->set($ext_id, $p);

        $p['state'] = Svc::SUCCESS;
        $this->svc->set($ext_id, $p);

        return redirect()->route('pay.success', ['ext_id' => $ext_id]);
    }

    // GET /pay/{ext_id}/success
    public function success(string $ext_id)
    {
        $p = $this->svc->get($ext_id);
        if (!$p) abort(404);
        if ($p['state'] !== Svc::SUCCESS) {
            return redirect()->route('pay.form', ['ext_id' => $ext_id]);
        }
        return view('pages.fake_debit.success', ['p' => $p]);
    }
}
