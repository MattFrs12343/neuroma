<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrador - NeuroSmart</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-slate-50/50 font-sans antialiased">

    {{-- HEADER --}}
    <header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-200/80 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <x-logo class="h-12 w-auto" />
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right hidden md:block">
                    <span class="text-xs text-slate-400 font-semibold block">Painel Geral</span>
                    <span class="text-sm font-bold text-slate-800">{{ session('admin_nombres') }} {{ session('admin_apellidos') }}</span>
                </div>
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-md shadow-purple-500/10">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m11.142 0L21.75 12l-4.179-2.25M12 5.25 15.75 7.5 12 9.75 8.25 7.5 12 5.25Zm0 11.25L15.75 14.25 12 16.5l-3.75-2.25L12 16.5Z"/></svg>
                    Admin
                </span>
                <a href="{{ route('admin.logout') }}" class="flex items-center gap-1.5 px-3.5 py-2 border border-slate-200 hover:border-red-200 hover:text-red-600 hover:bg-red-50 text-slate-600 font-bold text-xs rounded-xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"/></svg>
                    Sair
                </a>
            </div>
        </div>
    </header>

    {{-- NAVIGATION TABS --}}
    <nav class="bg-white border-b border-slate-200 sticky top-16 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center gap-1.5 overflow-x-auto">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 py-4 px-5 text-sm font-bold transition-all relative border-b-2 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'border-cyan-500 text-cyan-600 bg-cyan-50/20' : 'border-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-50' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                Laudos Neurológicos
            </a>
            <a href="{{ route('admin.clinicas.index') }}" class="flex items-center gap-2 py-4 px-5 text-sm font-bold transition-all relative border-b-2 shrink-0 {{ request()->routeIs('admin.clinicas.*') ? 'border-cyan-500 text-cyan-600 bg-cyan-50/20' : 'border-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-50' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.053.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z"/></svg>
                Clínicas Conveniadas
            </a>
            <a href="{{ route('admin.admins.index') }}" class="flex items-center gap-2 py-4 px-5 text-sm font-bold transition-all relative border-b-2 shrink-0 {{ request()->routeIs('admin.admins.*') ? 'border-cyan-500 text-cyan-600 bg-cyan-50/20' : 'border-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-50' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m11.142 0L21.75 12l-4.179-2.25M12 5.25 15.75 7.5 12 9.75 8.25 7.5 12 5.25Zm0 11.25L15.75 14.25 12 16.5l-3.75-2.25L12 16.5Z"/></svg>
                Administradores
            </a>
            <a href="{{ route('admin.usuarios.index') }}" class="flex items-center gap-2 py-4 px-5 text-sm font-bold transition-all relative border-b-2 shrink-0 {{ request()->routeIs('admin.usuarios.*') ? 'border-cyan-500 text-cyan-600 bg-cyan-50/20' : 'border-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-50' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/></svg>
                Usuários de Clínicas
            </a>

        </div>
    </nav>

    {{-- ALERTS --}}
    <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-6">
        <x-alert type="success" :message="session('success')" />
        <x-alert type="error" :message="session('error')" />
    </div>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        @yield('admin_content')
    </main>

    {{-- DELETE CONFIRMATION MODAL (shared) --}}
    <x-modal id="deleteModal" title="Confirmar Exclusão">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-50 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8 text-red-500"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
            </div>
            <p class="mt-3 text-sm text-slate-500" id="deleteModalMessage"></p>
            <p class="mt-2 text-xs text-red-500 bg-red-50/50 p-2.5 rounded-lg border border-red-100">Esta ação não pode ser desfeita.</p>
        </div>
        <x-slot name="actions">
            <button type="button" onclick="closeDeleteModal()" class="w-full sm:w-auto px-5 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-100 font-medium text-sm transition-all active:scale-[0.98]">Cancelar</button>
            <button type="button" onclick="submitDelete()" class="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 font-semibold text-sm shadow-md shadow-red-500/10 hover:shadow-lg transition-all active:scale-[0.98]">Sim, Excluir</button>
        </x-slot>
    </x-modal>

    <script>
        let deleteFormId = null;

        function confirmDelete(laudoIdClinica, laudoIdLaudo, patientName) {
            deleteFormId = 'deleteForm';
            document.getElementById('deleteModalMessage').innerHTML = 'Tem certeza de que deseja excluir o laudo de <strong class="text-slate-800 font-semibold">' + patientName + '</strong>?';
            const form = document.getElementById(deleteFormId);
            if (form) form.action = '/admin/delete-laudo/' + encodeURIComponent(laudoIdClinica) + '/' + encodeURIComponent(laudoIdLaudo);
            document.getElementById('deleteModal').classList.remove('hidden');
            document.getElementById('deleteModal').classList.add('flex');
        }

        function confirmDeleteEntity(formId, entityType, entityName) {
            deleteFormId = formId;
            document.getElementById('deleteModalMessage').innerHTML = 'Tem certeza de que deseja excluir ' + entityType + ' <strong class="text-slate-800 font-semibold">' + entityName + '</strong>?';
            document.getElementById('deleteModal').classList.remove('hidden');
            document.getElementById('deleteModal').classList.add('flex');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.getElementById('deleteModal').classList.remove('flex');
            deleteFormId = null;
        }

        function submitDelete() {
            if (deleteFormId) {
                document.getElementById(deleteFormId).submit();
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeDeleteModal();
        });
    </script>
</body>
</html>
