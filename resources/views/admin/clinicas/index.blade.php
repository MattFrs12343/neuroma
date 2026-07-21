@extends('admin.layout')

@section('admin_content')
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Gerenciar Clínicas Conveniadas</h1>
        <p class="text-sm text-slate-500 mt-1">Gerencie os estabelecimentos de saúde habilitados a consumir o sistema de laudos.</p>
    </div>
    <button onclick="openCreateModal()" class="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-3 bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-purple-500/20 hover:shadow-xl transition-all transform hover:-translate-y-0.5">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Nova Clínica Parceira
    </button>
</div>

{{-- TABLE --}}
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Nome da Clínica</th>
                    <th class="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Contato / Telefone</th>
                    <th class="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Endereço</th>
                    <th class="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($clinicasPaginated as $clinica)
                <tr class="hover:bg-slate-50/50 transition-all">
                    <td class="py-4 px-6 font-bold text-slate-900">{{ $clinica['NOMBRE'] }}</td>
                    <td class="py-4 px-6 text-sm text-slate-600 font-medium">
                        <span class="block">{{ $clinica['EMAIL'] ?? 'Não cadastrado' }}</span>
                        <span class="block text-xs text-slate-400">{{ $clinica['TELEFONO'] ?? '' }}</span>
                    </td>
                    <td class="py-4 px-6 text-xs text-slate-500 font-medium max-w-xs truncate">{{ $clinica['DIRECCION'] ?? '' }}</td>
                    <td class="py-4 px-6 text-right">
                        <div class="inline-flex gap-2">
                            <button onclick="openEditModal({{ Js::from($clinica['id']) }}, {{ Js::from($clinica['NOMBRE'] ?? '') }}, {{ Js::from($clinica['DIRECCION'] ?? '') }}, {{ Js::from($clinica['TELEFONO'] ?? '') }}, {{ Js::from($clinica['EMAIL'] ?? '') }})" class="px-3.5 py-2 bg-slate-50 hover:bg-cyan-50 text-slate-600 hover:text-cyan-700 border border-slate-200 hover:border-cyan-200 rounded-xl text-xs font-bold transition-all">Editar</button>
                            <button onclick="document.getElementById('deleteClinicaForm').action='{{ route('admin.clinicas.destroy', $clinica['id']) }}';confirmDeleteEntity('deleteClinicaForm', 'a clínica', {{ Js::from($clinica['NOMBRE'] ?? '') }})" class="px-3.5 py-2 bg-slate-50 hover:bg-red-50 text-slate-600 hover:text-red-700 border border-slate-200 hover:border-red-200 rounded-xl text-xs font-bold transition-all">Excluir</button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <x-pagination :paginator="$clinicasPaginated" />
</div>

{{-- CREATE/EDIT MODAL --}}
<div id="clinicaModal" class="fixed inset-0 z-50 items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm hidden" onclick="if(event.target===this) closeClinicaModal()">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-100 overflow-hidden animate-scale-up">
        <div class="bg-slate-50 px-6 py-4 border-b border-slate-200/80 flex items-center justify-between">
            <h3 class="text-base font-extrabold text-slate-800 tracking-tight uppercase" id="clinicaModalTitle">Nova Clínica</h3>
            <button onclick="closeClinicaModal()" class="p-1 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="clinicaForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="clinicaMethod" value="POST">
            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                <div>
                    <label class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Nome da Clínica *</label>
                    <input type="text" name="NOMBRE" id="clinicaNombre" required class="block w-full px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none" placeholder="Ex: Clínica Neuro-Vida">
                </div>
                <div>
                    <label class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Endereço Completo</label>
                    <input type="text" name="DIRECCION" id="clinicaDireccion" class="block w-full px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none" placeholder="Av, Rua, Número, Bairro, Cidade...">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Telefone</label>
                        <input type="text" name="TELEFONO" id="clinicaTelefono" class="block w-full px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none" placeholder="(11) 99999-9999">
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">E-mail</label>
                        <input type="email" name="EMAIL" id="clinicaEmail" class="block w-full px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none" placeholder="contato@clinica.com">
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 px-6 py-4 flex justify-end gap-2.5 border-t border-slate-200">
                <button type="button" onclick="closeClinicaModal()" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-100 font-bold text-xs transition-all active:scale-95">Cancelar</button>
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-[#1a9fc9] to-[#0d7fa3] text-white rounded-xl hover:opacity-90 font-bold text-xs shadow-md shadow-cyan-500/10 transition-all transform active:scale-95">Salvar Dados</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('clinicaModalTitle').textContent = 'Nova Clínica';
        document.getElementById('clinicaForm').action = '{{ route("admin.clinicas.store") }}';
        document.getElementById('clinicaMethod').value = 'POST';
        document.getElementById('clinicaNombre').value = '';
        document.getElementById('clinicaDireccion').value = '';
        document.getElementById('clinicaTelefono').value = '';
        document.getElementById('clinicaEmail').value = '';
        showModal('clinicaModal');
    }

    function openEditModal(id, nombre, direccion, telefono, email) {
        document.getElementById('clinicaModalTitle').textContent = 'Editar Clínica';
        document.getElementById('clinicaForm').action = '/admin/clinicas/' + encodeURIComponent(id);
        document.getElementById('clinicaMethod').value = 'PUT';
        document.getElementById('clinicaNombre').value = nombre;
        document.getElementById('clinicaDireccion').value = direccion;
        document.getElementById('clinicaTelefono').value = telefono;
        document.getElementById('clinicaEmail').value = email;
        showModal('clinicaModal');
    }

    function closeClinicaModal() { hideModal('clinicaModal'); }
    function showModal(id) { const el = document.getElementById(id); el.classList.remove('hidden'); el.classList.add('flex'); }
    function hideModal(id) { const el = document.getElementById(id); el.classList.add('hidden'); el.classList.remove('flex'); }

    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') { closeClinicaModal(); } });
</script>
@endsection

{{-- Single delete form (action set dynamically) --}}
<form id="deleteClinicaForm" method="POST" action="" class="hidden">
    @csrf @method('DELETE')
</form>
