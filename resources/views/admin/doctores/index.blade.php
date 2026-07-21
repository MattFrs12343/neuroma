{{-- admin/doctores/index.blade.php — Listado de doctores --}}

@extends('admin.layout')

@section('admin_content')

<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Doctores Revisores</h1>
        <p class="text-sm text-slate-500 mt-1">Administre los doctores habilitados para revisar y aprobar laudos.</p>
    </div>
    <a href="{{ route('admin.doctores.create') }}"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-500/10 hover:shadow-lg transition-all transform active:scale-95">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Nuevo Doctor
    </a>
</div>

{{-- DOCTORES LIST --}}
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mt-6">
    @if(count($doctores) > 0)
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Nombre</th>
                    <th class="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Usuario</th>
                    <th class="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($doctores as $doctor)
                <tr class="hover:bg-slate-50/80 transition-all">
                    <td class="py-4 px-6">
                        <div class="font-bold text-slate-900 text-sm">{{ $doctor['NOMBRE'] ?? '—' }}</div>
                    </td>
                    <td class="py-4 px-6">
                        <span class="text-sm text-slate-600">{{ $doctor['USUARIO'] ?? '—' }}</span>
                    </td>
                    <td class="py-4 px-6 text-right">
                        <button onclick="confirmDeleteEntity('deleteDoctorForm_{{ $doctor['id'] }}', 'el doctor', {{ Js::from($doctor['NOMBRE'] ?? '') }})"
                            class="p-2 hover:bg-red-50 text-slate-500 hover:text-red-600 rounded-xl transition-all" title="Eliminar Doctor">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                        </button>
                        <form id="deleteDoctorForm_{{ $doctor['id'] }}" method="POST" action="{{ route('admin.doctores.destroy', $doctor['id']) }}" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="text-center py-16">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-14 h-14 text-slate-300 mx-auto mb-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
        <p class="text-base font-bold text-slate-800">Nenhum doctor registrado</p>
        <p class="text-xs text-slate-400 mt-1">Cree el primer doctor para habilitar la revisión de laudos.</p>
    </div>
    @endif
</div>

@stop
