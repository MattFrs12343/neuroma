<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar PDF</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="fixed inset-0 bg-slate-900 flex flex-col">

    {{-- Dark top bar --}}
    <div class="bg-slate-950 text-white h-16 px-4 flex items-center justify-between border-b border-slate-800 shrink-0">
        <div class="flex items-center gap-3">
            <button onclick="history.back()" class="p-2 hover:bg-slate-800 rounded-xl text-slate-400 hover:text-white transition-all flex items-center justify-center" title="Voltar ao Painel">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            </button>
            <div>
                <h1 class="text-sm font-bold truncate max-w-xs sm:max-w-md">Laudo Médico</h1>
                <span class="text-[11px] text-slate-400 font-semibold block uppercase tracking-wider">NeuroIsbe</span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-md transition-all active:scale-[0.98]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                <span class="hidden sm:inline">Baixar PDF</span>
            </button>
        </div>
    </div>

    {{-- PDF iframe --}}
    <iframe
        src="{{ $pdfUrl }}"
        class="flex-1 w-full border-0"
        allowfullscreen>
    </iframe>

</body>
</html>
