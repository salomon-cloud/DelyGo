<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <!-- Usar assets tradicionales en public/ (sin Vite ni Inertia) -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="{{ asset('js/app.js') }}" defer></script>
    </head>
    <body class="font-sans antialiased">
        @include('components.nav')
        <div id="app">
            @yield('content')
        </div>
        <!-- Toast container -->
        <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2 pointer-events-none"></div>

        <!-- Result modal (shows success/error messages) -->
        <div id="result-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); align-items:center; justify-content:center; z-index:9999;">
            <div style="background:white; border-radius:8px; width:90%; max-width:480px; margin:0 auto; padding:16px;" role="dialog" aria-modal="true">
                <div id="result-modal-body" class="text-center">
                    <h3 id="result-modal-title" class="text-lg font-semibold mb-2"></h3>
                    <p id="result-modal-message" class="mb-4"></p>
                    <div class="flex justify-center">
                        <button id="result-modal-ok" class="px-4 py-2 bg-blue-600 text-white rounded">OK</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // showToast(message, type) -> type: 'success'|'error'|'info'
            function showToast(message, type = 'info'){
                try{
                    const container = document.getElementById('toast-container');
                    if(!container) return;
                    const el = document.createElement('div');
                    el.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                    el.className = 'max-w-sm p-3 rounded shadow pointer-events-auto';
                    if(type === 'success') el.style.background = '#ecfdf5', el.style.color = '#065f46';
                    else if(type === 'error') el.style.background = '#fee2e2', el.style.color = '#991b1b';
                    else el.style.background = '#f3f4f6', el.style.color = '#111827';
                    el.textContent = message;
                    container.appendChild(el);
                    setTimeout(()=>{
                        el.style.opacity = '0';
                        el.style.transform = 'translateY(-6px)';
                        setTimeout(()=> el.remove(), 400);
                    }, 3800);
                }catch(e){ console.error('showToast error', e); }
            }

            // showResultModal(title, message, success)
            function showResultModal(title, message, success = true, timeoutMs = 0){
                try{
                    const modal = document.getElementById('result-modal');
                    const titleEl = document.getElementById('result-modal-title');
                    const msgEl = document.getElementById('result-modal-message');
                    const ok = document.getElementById('result-modal-ok');
                    if(!modal || !titleEl || !msgEl || !ok) return;
                    titleEl.textContent = title || (success ? 'Éxito' : 'Error');
                    msgEl.textContent = message || '';
                    // show modal using inline style (avoid relying on Tailwind)
                    modal.style.display = 'flex';
                    ok.focus();
                    ok.onclick = function(){ modal.style.display = 'none'; };
                    // Auto-hide after timeoutMs if provided (>0)
                    if (timeoutMs && Number(timeoutMs) > 0) {
                        setTimeout(() => { try{ modal.style.display = 'none'; }catch(e){} }, Number(timeoutMs));
                    }
                }catch(e){ console.error('showResultModal error', e); }
            }
        </script>
    </body>
</html>
