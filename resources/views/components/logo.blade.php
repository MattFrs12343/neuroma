@props(['variant' => 'header'])

@if($variant === 'login')
{{-- Login page: text-only brand --}}
<div {{ $attributes->merge(['class' => 'flex flex-col items-center gap-4']) }}>
    <h1 class="text-4xl font-black text-slate-800 tracking-tight">
        <span class="bg-gradient-to-r from-cyan-500 to-blue-600 bg-clip-text text-transparent">Neuro</span>MA
    </h1>
    <p class="text-base text-slate-400 font-medium">Sistema de Laudos Neurológicos</p>
</div>
@else
{{-- Header: compact icon + brand --}}
<div {{ $attributes->merge(['class' => 'flex items-center gap-2']) }}>
    
    <span class="text-xl font-extrabold text-slate-800 tracking-tight">
        <span class="bg-gradient-to-r from-cyan-500 to-blue-600 bg-clip-text text-transparent">Neuro</span>MA
    </span>
</div>
@endif
