<!doctype html>
<html lang="en"><head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Form</title>
    <link rel="stylesheet" href="https://unpkg.com/mvp.css">
</head><body>
<main>
    <h2>Pay {{ $p['amount'] }} {{ $p['currency'] }}</h2>
    <p>{{ $p['description'] }}</p>

    <form method="post" action="{{ route('pay.submit', ['ext_id' => $p['ext_id']]) }}">
        @csrf
        <label>Card number
            <input type="text" name="card_number" placeholder="8600 1234 5678 9012" required>
        </label>
        <label>Expire (MM/YY)
            <input type="text" name="expire" placeholder="12/27" required>
        </label>
        <label>CVV
            <input type="password" name="cvv" placeholder="***" required>
        </label>
        <button type="submit">Pay</button>
    </form>

    <p style="margin-top:1rem; font-size:0.9em;">ext_id: <code>{{ $p['ext_id'] }}</code></p>
</main>
</body></html>
