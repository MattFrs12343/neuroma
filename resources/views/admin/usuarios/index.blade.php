@extends('admin.layout')

@section('admin_content')
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Gerenciar Usuários de Clínicas</h1>
        <p class="text-sm text-slate-500 mt-1">Controle de acesso para os profissionais clínicos visualizarem os PDFs de cada clínica parceira.</p>
    </div>
    <button onclick="openCreateModal()" class="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-3 bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-purple-500/20 hover:shadow-xl transition-all transform hover:-translate-y-0.5">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Novo Usuário Clínico
    </button>
</div>

{{-- TABLE --}}
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Nome Completo</th>
                    <th class="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Nome de Usuário (Login)</th>
                    <th class="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Clínica Vinculada</th>
                    <th class="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($usersPaginated as $user)
                @php
                    $clinicaVinculada = '';
                    foreach($clinicas as $c) { if($c['id'] === ($user['ID_CLINICA'] ?? '')) { $clinicaVinculada = $c['nombre']; break; } }
                @endphp
                <tr class="hover:bg-slate-50/50 transition-all">
                    <td class="py-4 px-6 font-bold text-slate-900">{{ $user['NOMBRES'] }} {{ $user['APELLIDOS'] }}</td>
                    <td class="py-4 px-6 text-sm text-slate-600 font-semibold">{{ $user['USUARIO'] }}</td>
                    <td class="py-4 px-6">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-100 uppercase tracking-wide">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.053.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z"/></svg>
                            {{ $clinicaVinculada ?: 'Nenhuma' }}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-right">
                        <div class="inline-flex gap-2">
                            <button onclick="openEditModal({{ Js::from($user['id']) }}, {{ Js::from($user['USUARIO'] ?? '') }}, {{ Js::from($user['NOMBRES'] ?? '') }}, {{ Js::from($user['APELLIDOS'] ?? '') }}, {{ Js::from($user['ID_CLINICA'] ?? '') }})" class="px-3.5 py-2 bg-slate-50 hover:bg-cyan-50 text-slate-600 hover:text-cyan-700 border border-slate-200 hover:border-cyan-200 rounded-xl text-xs font-bold transition-all">Editar</button>
                            <button onclick="document.getElementById('deleteUserForm').action='{{ route('admin.usuarios.destroy', $user['id']) }}';confirmDeleteEntity('deleteUserForm', 'o usuário', {{ Js::from($user['USUARIO'] ?? '') }})" class="px-3.5 py-2 bg-slate-50 hover:bg-red-50 text-slate-600 hover:text-red-700 border border-slate-200 hover:border-red-200 rounded-xl text-xs font-bold transition-all">Excluir</button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <x-pagination :paginator="$usersPaginated" />
</div>

{{-- CREATE/EDIT MODAL --}}
<div id="userModal" class="fixed inset-0 z-50 items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm hidden" onclick="if(event.target===this) closeUserModal()">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-100 overflow-hidden animate-scale-up">
        <div class="bg-slate-50 px-6 py-4 border-b border-slate-200/80 flex items-center justify-between">
            <h3 class="text-base font-extrabold text-slate-800 tracking-tight uppercase" id="userModalTitle">Novo Usuário</h3>
            <button onclick="closeUserModal()" class="p-1 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="userForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="userMethod" value="POST">
            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Nomes *</label>
                        <input type="text" name="NOMBRES" id="userNombres" required class="block w-full px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none" placeholder="Nome do profissional">
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Sobrenomes *</label>
                        <input type="text" name="APELLIDOS" id="userApellidos" required class="block w-full px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none" placeholder="Sobrenome do profissional">
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Nome de Usuário (Login) *</label>
                    <input type="text" name="USUARIO" id="userUsuario" required class="block w-full px-3.5 py-2.5 bg-slate-50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none disabled:opacity-60" placeholder="Ex: dralana">
                </div>
                <div>
                    <label class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Senha <span id="userPasswordOptional">*</span></label>
                    <input type="password" name="PASSWORD" id="userPassword" class="block w-full px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none" placeholder="••••••••••••">
                </div>
                <div>
                    <label class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Clínica Vinculada *</label>
                    <div class="relative">
                        <select name="ID_CLINICA" id="userClinica" class="block w-full px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none appearance-none">
                            <option value="">Nenhuma</option>
                            @foreach($clinicas as $clinica)
                                <option value="{{ $clinica['id'] }}">{{ $clinica['nombre'] }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                            <svg class="fill-current h-4 w-4" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 px-6 py-4 flex justify-end gap-2.5 border-t border-slate-200">
                <button type="button" onclick="closeUserModal()" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-100 font-bold text-xs transition-all active:scale-95">Cancelar</button>
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-[#1a9fc9] to-[#0d7fa3] text-white rounded-xl hover:opacity-90 font-bold text-xs shadow-md shadow-cyan-500/10 transition-all transform active:scale-95">Salvar Dados</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('userModalTitle').textContent = 'Novo Usuário';
        document.getElementById('userForm').action = '{{ route("admin.usuarios.store") }}';
        document.getElementById('userMethod').value = 'POST';
        document.getElementById('userUsuario').value = '';
        document.getElementById('userNombres').value = '';
        document.getElementById('userApellidos').value = '';
        document.getElementById('userClinica').value = '';
        showModal('userModal');
    }

    function openEditModal(id, usuario, nombres, apellidos, idClinica) {
        document.getElementById('userModalTitle').textContent = 'Editar Usuário';
        document.getElementById('userForm').action = '/admin/usuarios/' + encodeURIComponent(id);
        document.getElementById('userMethod').value = 'PUT';
        document.getElementById('userUsuario').value = usuario;
        document.getElementById('userPassword').required = false;
        document.getElementById('userPasswordOptional').textContent = '(deixe em branco para manter)';
        document.getElementById('userPassword').value = '';
        document.getElementById('userNombres').value = nombres;
        document.getElementById('userApellidos').value = apellidos;
        document.getElementById('userClinica').value = idClinica;
        showModal('userModal');
    }

    function closeUserModal() { hideModal('userModal'); }
    function showModal(id) { const el = document.getElementById(id); el.classList.remove('hidden'); el.classList.add('flex'); }
    function hideModal(id) { const el = document.getElementById(id); el.classList.add('hidden'); el.classList.remove('flex'); }

    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') { closeUserModal(); } });
</script>
@endsection

<form id="deleteUserForm" method="POST" action="" class="hidden">
    @csrf @method('DELETE')
</form>
