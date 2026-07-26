<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - NeuroSmart</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative min-h-screen bg-gradient-to-br from-slate-900 to-slate-800 flex items-center justify-center p-4 sm:p-6 lg:p-8 font-sans antialiased overflow-hidden">

    <canvas id="neuralCanvas" class="absolute inset-0 w-full h-full pointer-events-none z-0"></canvas>

    <div class="relative z-10 w-full max-w-md">

        {{-- Branding --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">
                <span class="bg-gradient-to-r from-cyan-500 to-blue-600 bg-clip-text text-transparent">Neuro</span>MB
            </h1>
            <p class="text-sm text-slate-400 font-medium mt-1">Sistema de Laudos Neurológicos</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 p-8">

            <div class="text-center mb-7">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600 shadow-lg shadow-cyan-500/20 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="white" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                </div>
                <h2 class="text-xl font-bold text-slate-800">Acesso Conveniado</h2>
                <p class="text-sm text-slate-400 mt-1">Entre com suas credenciais</p>
            </div>

            @if (session('message'))
            <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-100 text-red-700 text-sm font-medium flex items-start gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-red-500 shrink-0 mt-0.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                <span>{{ session('message') }}</span>
            </div>
            @endif

            @if ($errors->any())
            <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-100 text-red-700 text-sm font-medium flex items-start gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-red-500 shrink-0 mt-0.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Usuário</label>
                    <input type="text" name="username" required autocomplete="username" autofocus
                        class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 rounded-xl text-sm font-medium text-slate-800 placeholder-slate-400 transition-all outline-none"
                        placeholder="Digite seu usuário">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Senha</label>
                    <input type="password" name="password" required autocomplete="current-password"
                        class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 rounded-xl text-sm font-medium text-slate-800 placeholder-slate-400 transition-all outline-none"
                        placeholder="Digite sua senha">
                </div>
                <button type="submit"
                    class="w-full py-3 px-4 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-cyan-500/20 hover:shadow-xl hover:shadow-cyan-500/30 transition-all transform active:scale-[0.98] outline-none">
                    Entrar no Sistema
                </button>
            </form>

            <div class="mt-7 pt-5 border-t border-slate-100">
                <p class="text-xs text-slate-400 font-medium text-center mb-3">Outros acessos</p>
                <div class="flex items-center justify-center gap-3">
                    <a href="{{ route('admin.login.show') }}"
                        class="flex items-center gap-2 px-4 py-2.5 border border-purple-200 hover:border-purple-400 hover:bg-purple-50 text-purple-600 rounded-xl text-xs font-semibold transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m11.142 0L21.75 12l-4.179-2.25M12 5.25 15.75 7.5 12 9.75 8.25 7.5 12 5.25Zm0 11.25L15.75 14.25 12 16.5l-3.75-2.25L12 16.5Z"/></svg>
                        Administrador
                    </a>

                </div>
            </div>
        </div>

        <p class="text-center text-xs text-slate-400 mt-6">
            &copy; {{ date('Y') }} NeuroSmart. Todos os direitos reservados.
        </p>
    </div>

    <script>
        const canvas = document.getElementById('neuralCanvas');
        const ctx = canvas.getContext('2d');
        let animFrame;

        function resize() {
            canvas.width = canvas.parentElement.offsetWidth;
            canvas.height = canvas.parentElement.offsetHeight;
        }
        resize();
        window.addEventListener('resize', resize);

        const particleCount = 30;
        const particles = [];
        for (let i = 0; i < particleCount; i++) {
            particles.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                vx: (Math.random() - 0.5) * 0.6,
                vy: (Math.random() - 0.5) * 0.6,
                radius: Math.random() * 3 + 2,
                pulseSpeed: 0.02 + Math.random() * 0.03,
                pulseValue: Math.random() * Math.PI
            });
        }

        function draw() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            for (let i = 0; i < particleCount; i++) {
                const p1 = particles[i];
                for (let j = i + 1; j < particleCount; j++) {
                    const p2 = particles[j];
                    const dist = Math.hypot(p1.x - p2.x, p1.y - p2.y);
                    const maxDist = Math.min(canvas.width, canvas.height) * 0.25;
                    if (dist < maxDist) {
                        const alpha = (1 - dist / maxDist) * 0.15;
                        ctx.beginPath();
                        ctx.moveTo(p1.x, p1.y);
                        ctx.lineTo(p2.x, p2.y);
                        ctx.strokeStyle = `rgba(6,182,212,${alpha})`;
                        ctx.lineWidth = 1;
                        ctx.stroke();
                    }
                }
            }
            particles.forEach(p => {
                p.x += p.vx;
                p.y += p.vy;
                if (p.x < 0 || p.x > canvas.width) p.vx *= -1;
                if (p.y < 0 || p.y > canvas.height) p.vy *= -1;
                p.pulseValue += p.pulseSpeed;
                const scale = 1 + Math.sin(p.pulseValue) * 0.4;
                const opacity = 0.4 + (Math.sin(p.pulseValue) + 1) * 0.3;
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.radius * scale, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(6,182,212,${opacity})`;
                ctx.shadowColor = 'rgba(6,182,212,0.5)';
                ctx.shadowBlur = 8;
                ctx.fill();
                ctx.shadowBlur = 0;
            });
            animFrame = requestAnimationFrame(draw);
        }
        draw();
    </script>
</body>
</html>
