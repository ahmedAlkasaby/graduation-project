<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Attendance Kiosk') }}</title>
    <style>
        :root { --bg: #0b0f14; --panel: #131a22; --text: #e8eef4; --muted: #8a99a8; --accent: #3fb68b; --danger: #e5484d; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: var(--bg); color: var(--text); font-family: system-ui, -apple-system, "Segoe UI", sans-serif; display: flex; align-items: center; justify-content: center; padding: 16px; }
        .kiosk { width: 100%; max-width: 480px; text-align: center; }
        .clock { font-size: clamp(48px, 14vw, 88px); font-variant-numeric: tabular-nums; font-weight: 600; letter-spacing: 2px; }
        .date { color: var(--muted); margin-bottom: 32px; }
        form { display: flex; gap: 8px; }
        input { flex: 1; min-width: 0; padding: 14px 16px; font-size: 18px; border-radius: 10px; border: 1px solid #2a3542; background: var(--panel); color: var(--text); }
        button { padding: 14px 20px; font-size: 18px; border: 0; border-radius: 10px; background: var(--accent); color: #04130d; font-weight: 600; cursor: pointer; }
        button:disabled { opacity: .5; cursor: wait; }
        .message { min-height: 24px; margin: 16px 0; color: var(--danger); }
        .qr-wrap { position: relative; width: 340px; max-width: 100%; aspect-ratio: 1; margin: 0 auto; display: none; }
        .qr-wrap.visible { display: block; }
        .qr { position: absolute; inset: 22px; background: #fff; border-radius: 12px; padding: 8px; }
        .qr svg { width: 100%; height: 100%; display: block; }
        .ring { position: absolute; inset: 0; transform: rotate(-90deg); }
        .ring circle { fill: none; stroke-width: 6; }
        .ring .track { stroke: #22303d; }
        .ring .progress { stroke: var(--accent); stroke-linecap: round; transition: stroke-dashoffset 1s linear; }
        .countdown { margin-top: 12px; color: var(--muted); font-variant-numeric: tabular-nums; }
    </style>
</head>
<body>
<main class="kiosk">
    <div class="clock" id="clock">--:--:--</div>
    <div class="date" id="date"></div>

    <form id="qr-form" autocomplete="off">
        <input id="identifier" name="identifier" placeholder="{{ __('Employee code or email') }}" required maxlength="191" autofocus>
        <button type="submit" id="submit">{{ __('Show QR') }}</button>
    </form>
    <div class="message" id="message" role="status"></div>

    <div class="qr-wrap" id="qr-wrap">
        <svg class="ring" viewBox="0 0 100 100" aria-hidden="true">
            <circle class="track" cx="50" cy="50" r="47"></circle>
            <circle class="progress" id="ring" cx="50" cy="50" r="47"></circle>
        </svg>
        <div class="qr" id="qr"></div>
    </div>
    <div class="countdown" id="countdown"></div>
</main>

<script>
    (function () {
        const endpoint = @json(route('api.public.attendance.qr'));
        const circumference = 2 * Math.PI * 47;
        const ring = document.getElementById('ring');
        const form = document.getElementById('qr-form');
        const input = document.getElementById('identifier');
        const submit = document.getElementById('submit');
        const message = document.getElementById('message');
        const qrWrap = document.getElementById('qr-wrap');
        const qr = document.getElementById('qr');
        const countdown = document.getElementById('countdown');
        let timer = null;

        ring.style.strokeDasharray = circumference;

        function tickClock() {
            const now = new Date();
            document.getElementById('clock').textContent = now.toLocaleTimeString([], { hour12: false });
            document.getElementById('date').textContent = now.toLocaleDateString([], { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        }
        tickClock();
        setInterval(tickClock, 1000);

        function hideQr() {
            clearInterval(timer);
            qr.innerHTML = '';
            qrWrap.classList.remove('visible');
            countdown.textContent = '';
        }

        function showQr(svg, expiresAt, ttl) {
            hideQr();
            qr.innerHTML = svg;
            qrWrap.classList.add('visible');
            const end = new Date(expiresAt).getTime();
            const update = () => {
                const left = Math.max(0, Math.ceil((end - Date.now()) / 1000));
                ring.style.strokeDashoffset = circumference * (1 - left / ttl);
                countdown.textContent = left + 's';
                if (left === 0) { hideQr(); }
            };
            update();
            timer = setInterval(update, 1000);
        }

        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            message.textContent = '';
            submit.disabled = true;
            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ identifier: input.value }),
                });
                const body = await response.json().catch(() => ({}));
                if (!response.ok) {
                    hideQr();
                    message.textContent = body.message || @json(__('Unable to generate a QR code.'));
                    return;
                }
                showQr(body.data.svg, body.data.expires_at, body.data.ttl);
                input.value = '';
            } catch (error) {
                message.textContent = @json(__('Network error. Please try again.'));
            } finally {
                submit.disabled = false;
                input.focus();
            }
        });
    })();
</script>
</body>
</html>
