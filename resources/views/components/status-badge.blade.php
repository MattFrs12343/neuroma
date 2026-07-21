@props(['status' => 'pendiente'])

@php
    $map = [
        'pendiente' => ['label' => 'Pendiente', 'class' => 'bg-amber-50 text-amber-700 border-amber-200'],
        'aprovado' => ['label' => 'Aprobado', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
        'rechazado' => ['label' => 'Rechazado', 'class' => 'bg-red-50 text-red-700 border-red-200'],
    ];
    $state = $map[$status] ?? $map['pendiente'];
@endphp

<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $state['class'] }} uppercase tracking-wide">
    {{ $state['label'] }}
</span>
