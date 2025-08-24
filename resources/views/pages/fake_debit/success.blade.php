<!doctype html>
<html lang="en"><head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Success</title>
    <link rel="stylesheet" href="https://unpkg.com/mvp.css">
</head><body>
<main>
    <h2>Payment Successful ✅</h2>
    <ul>
        <li>ext_id: <code>{{ $p['ext_id'] }}</code></li>
        <li>amount: {{ $p['amount'] }} {{ $p['currency'] }}</li>
        <li>card: {{ $p['card']['masked'] ?? '' }}</li>
        <li>state: {{ $p['state'] }}</li>
    </ul>
    <p>You can now close this page.</p>
</main>
</body></html>
