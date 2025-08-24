<!doctype html>
<html lang="en"><head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>3‑D Secure</title>
    <link rel="stylesheet" href="https://unpkg.com/mvp.css">
</head><body>
<main>
    <h2>3‑D Secure</h2>
    <p>We sent a one‑time code to your phone. Enter any 6 digits to continue.</p>

    <form method="post" action="{{ route('pay.3ds.submit', ['ext_id' => $p['ext_id']]) }}">
        @csrf
        <label>One‑time code
            <input type="text" name="otp" minlength="4" maxlength="8" placeholder="123456" required>
        </label>
        <button type="submit">Confirm</button>
    </form>

    <p style="margin-top:1rem; font-size:0.9em;">Masked card: <code>{{ $p['card']['masked'] ?? '' }}</code></p>
</main>
</body></html>
