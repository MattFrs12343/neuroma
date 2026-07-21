{{-- admin/doctores/create.blade.php — Crear doctor --}}

@extends('admin.layout')

@section('admin_content')

<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.doctores.index') }}" class="p-2 hover:bg-slate-100 rounded-xl transition-all text-slate-400 hover:text-slate-600">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Nuevo Doctor</h1>
            <p class="text-sm text-slate-500 mt-1">Registre un nuevo doctor revisor en el sistema.</p>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-8">
        <form action="{{ route('admin.doctores.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Nombre Completo</label>
                <input type="text" name="NOMBRE" value="{{ old('NOMBRE') }}" required
                    class="block w-full px-4 py-3 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-emerald-500 rounded-xl text-sm font-medium transition-all outline-none"
                    placeholder="Dr. Juan Pérez">
                @error('NOMBRE')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Usuario</label>
                <input type="text" name="USUARIO" value="{{ old('USUARIO') }}" required
                    class="block w-full px-4 py-3 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-emerald-500 rounded-xl text-sm font-medium transition-all outline-none"
                    placeholder="juan.perez">
                @error('USUARIO')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Contraseña</label>
                <input type="password" name="PASSWORD" required
                    class="block w-full px-4 py-3 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-emerald-500 rounded-xl text-sm font-medium transition-all outline-none"
                    placeholder="Mínimo 4 caracteres">
                @error('PASSWORD')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.doctores.index') }}"
                    class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-100 font-medium text-sm transition-all">Cancelar</a>
                <button type="submit"
                    class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white rounded-xl font-semibold text-sm shadow-md shadow-emerald-500/10 hover:shadow-lg transition-all active:scale-95">
                    Registrar Doctor
                </button>
            </div>
        </form>
    </div>
</div>

@stop
