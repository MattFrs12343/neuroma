<x-layout role="user" title="Dashboard - NeuroMA">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Painel de Laudos Neurológicos</h1>
                <p class="text-sm text-slate-500 mt-1">Busque, visualize e faça o download de laudos assinados digitalmente.</p>
            </div>
        </div>

        {{-- FILTERS --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-4">
            <form action="{{ route('dashboard') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3.5">
                    <div class="col-span-1 md:col-span-4">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Pesquisa Avançada</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.608 10.608Z"/></svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none"
                                placeholder="Nome, documento, código do laudo...">
                        </div>
                    </div>
                    <div class="col-span-1 md:col-span-4">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Data Início</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                            </div>
                            <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}"
                                class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none">
                        </div>
                    </div>
                    <div class="col-span-1 md:col-span-4">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Data Fim</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                            </div>
                            <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}"
                                class="block w-full pl-10 pr-4 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none">
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-100 mt-4">
                    @if(request('search') || request('fecha_inicio') || request('fecha_fin'))
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-1.5 px-4 py-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 text-xs font-bold rounded-xl transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        Limpar Filtros
                    </a>
                    @endif
                    <button type="submit" class="flex items-center gap-1.5 px-5 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-cyan-500/10 hover:shadow-lg transition-all transform active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z"/></svg>
                        Filtrar Busca
                    </button>
                </div>
            </form>
        </div>

        {{-- LAUDOS TABLE --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            {{-- Mobile Cards --}}
            <div class="block md:hidden p-4 space-y-3 bg-slate-50/50">
                @forelse($laudos as $laudo)
                @php $initials = strtoupper(mb_substr($laudo['nombres'], 0, 1)); @endphp
                <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="h-11 w-11 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center text-white font-extrabold text-base shadow-sm">{{ $initials }}</div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-slate-900 truncate">{{ $laudo['nombres'] }}</h4>
                            <span class="text-[11px] text-slate-500 font-medium block">Exame: {{ preg_replace('/^(\d{2})\/(\d{2})\/(\d{4})$/', '$3/$2/$1', $laudo['fecha_estudio']) }}</span>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2 pt-2 border-t border-slate-50">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-cyan-50 text-cyan-700 border border-cyan-100 uppercase tracking-wide">{{ $laudo['tipo_estudio'] }}</span>
                        {{-- ESTADO badge — todos los laudos visibles son aprobados --}}
                        <x-status-badge status="aprovado" />
                    </div>
                    <div class="flex gap-2 pt-1.5">
                        <a href="{{ route('ver.pdf', ['id_clinica' => session('id_clinica'), 'id_laudo' => $laudo['id_documento']]) }}" target="_blank" class="flex-1 py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                            Ver
                        </a>
                        <a href="{{ route('download.pdf', ['id_clinica' => session('id_clinica'), 'id_laudo' => $laudo['id_documento']]) }}" class="flex-1 py-2 px-3 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-100 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-emerald-600"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                            Baixar
                        </a>
                    </div>
                </div>
                @empty
                <div class="text-center py-12 bg-white rounded-2xl border-2 border-dashed border-slate-200 p-6">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-12 h-12 text-slate-300 mx-auto mb-3"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                    <p class="text-sm font-bold text-slate-700">No se encontraron laudos</p>
                    <p class="text-xs text-slate-400 mt-1">Ajuste os filtros de data ou barra de pesquisa para tentar novamente.</p>
                </div>
                @endforelse
            </div>

            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Paciente</th>
                            <th class="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Estudo / Exame</th>
                            <th class="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Estado</th>
                            <th class="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($laudos as $laudo)
                        @php $initials = strtoupper(mb_substr($laudo['nombres'], 0, 1)); @endphp
                        <tr class="hover:bg-slate-50/80 transition-all">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3.5">
                                    <div class="h-11 w-11 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center text-white font-extrabold text-sm shadow-sm">{{ $initials }}</div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-[14px]">{{ $laudo['nombres'] }}</div>
                                        <div class="text-slate-400 text-xs mt-0.5 flex items-center gap-1.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                                            Data do exame: {{ preg_replace('/^(\d{2})\/(\d{2})\/(\d{4})$/', '$3/$2/$1', $laudo['fecha_estudio']) }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-cyan-50 text-cyan-700 border border-cyan-100 uppercase tracking-wide">{{ $laudo['tipo_estudio'] }}</span>
                            </td>
                            <td class="py-4 px-6">
                                {{-- ESTADO badge — todos los laudos visibles son aprobados --}}
                                <x-status-badge status="aprovado" />
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="inline-flex gap-2">
                                    <a href="{{ route('ver.pdf', ['id_clinica' => session('id_clinica'), 'id_laudo' => $laudo['id_documento']]) }}" target="_blank" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all shadow-sm active:scale-95">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                                        Ver
                                    </a>
                                    <a href="{{ route('download.pdf', ['id_clinica' => session('id_clinica'), 'id_laudo' => $laudo['id_documento']]) }}" class="px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-150 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all shadow-sm active:scale-95">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-emerald-600"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                        Baixar
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-16">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-14 h-14 text-slate-300 mx-auto mb-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                <p class="text-base font-bold text-slate-800">No se encontraron laudos</p>
                                <p class="text-xs text-slate-400 mt-1">Experimente alterar a data ou termo de pesquisa.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            <x-pagination :paginator="$laudos" />
        </div>
</x-layout>
