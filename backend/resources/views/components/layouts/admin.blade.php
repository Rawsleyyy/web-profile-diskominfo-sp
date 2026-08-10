<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="admin-session-expires-at" content="{{ (int) session('admin_session_expires_at', 0) }}">
    <meta name="server-now" content="{{ now()->timestamp }}">

    <title>{{ $title ?? 'Dashboard Admin' }}</title>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-900">
    @include('partials.dashboard.sidebar')

    <div class="min-h-screen pl-64">
        @include('partials.dashboard.header')

        <main class="min-h-[calc(100vh-80px)]">
            {{ $slot }}
        </main>

        @include('partials.dashboard.footer')
    </div>

    @livewireScripts

    <script>
        (() => {
            const expiresAt = Number(
                document.querySelector('meta[name="admin-session-expires-at"]')?.content || 0
            );
            const serverNow = Number(
                document.querySelector('meta[name="server-now"]')?.content || 0
            );
            const countdown = document.getElementById('admin-session-countdown');

            if (!expiresAt || !serverNow) return;

            const deadline = Date.now() + Math.max(0, expiresAt - serverNow) * 1000;
            let logoutStarted = false;

            const formatTime = (totalSeconds) => {
                const minutes = Math.floor(totalSeconds / 60);
                const seconds = totalSeconds % 60;
                return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            };

            const forceLogout = async () => {
                if (logoutStarted) return;
                logoutStarted = true;

                try {
                    await fetch(@json(route('admin.logout')), {
                        method: 'POST',
                        credentials: 'same-origin',
                        keepalive: true,
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        },
                    });
                } catch (error) {
                    // Redirect tetap dilakukan walaupun request logout gagal.
                } finally {
                    const loginUrl = new URL(
                        @json((string) config('app.admin_login_url', route('admin.login'))),
                        window.location.origin
                    );
                    loginUrl.searchParams.set('expired', '1');
                    window.location.replace(loginUrl.toString());
                }
            };

            const tick = () => {
                const remaining = Math.max(0, Math.ceil((deadline - Date.now()) / 1000));

                if (countdown) {
                    countdown.textContent = formatTime(remaining);
                    countdown.classList.toggle('text-red-600', remaining <= 60);
                }

                if (remaining <= 0) {
                    forceLogout();
                }
            };

            tick();
            window.setInterval(tick, 1000);
            document.addEventListener('visibilitychange', tick);
        })();
    </script>
</body>
</html>
