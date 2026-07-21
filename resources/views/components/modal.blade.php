@props(['id' => 'modal', 'title' => 'Confirmar'])

<div id="{{ $id }}" class="fixed inset-0 z-50 items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm hidden"
     onclick="if(event.target===this) document.getElementById('{{ $id }}').classList.add('hidden')">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden animate-scale-up">
        {{-- Title --}}
        <div class="p-6 text-center">
            <h3 class="text-xl font-bold text-slate-900">{{ $title }}</h3>
        </div>

        {{-- Body (slot) --}}
        <div class="px-6 pb-4">
            {{ $slot }}
        </div>

        {{-- Actions --}}
        @if(isset($actions))
        <div class="bg-slate-50 px-6 py-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-2.5">
            {{ $actions }}
        </div>
        @endif
    </div>
</div>
