@props(['paginator', 'activeClass' => 'bg-gradient-to-r from-cyan-500 to-blue-600 text-white shadow-md shadow-cyan-500/15'])

@if($paginator->hasPages())
<div class="bg-white border-t border-slate-200 px-5 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
    <div class="text-xs font-semibold text-slate-500">
        Mostrando <span class="text-slate-800">{{ $paginator->firstItem() }}</span> a <span class="text-slate-800">{{ $paginator->lastItem() }}</span> de <span class="text-slate-800">{{ $paginator->total() }}</span> resultados
    </div>
    <div class="flex items-center gap-1.5">
        @if(!$paginator->onFirstPage())
        <a href="{{ $paginator->previousPageUrl() }}" class="px-3.5 py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 transition-all flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Anterior
        </a>
        @endif
        @foreach($paginator->getUrlRange(max($paginator->currentPage() - 2, 1), min($paginator->currentPage() + 2, $paginator->lastPage())) as $page => $url)
        <a href="{{ $url }}"
            class="h-9 w-9 flex items-center justify-center rounded-xl text-xs font-bold transition-all {{ $paginator->currentPage() === $page ? $activeClass : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            {{ $page }}
        </a>
        @endforeach
        @if($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="px-3.5 py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 transition-all flex items-center gap-1">
            Próximo
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
        </a>
        @endif
    </div>
</div>
@endif
