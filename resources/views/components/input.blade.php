@props(['label' => null, 'error' => null, 'hint' => null, 'required' => false])
<div>
    @if($label)
        <label class="mb-1.5 block text-xs font-semibold text-slate-700 uppercase tracking-wider">
            {{ $label }}
            @if($required)<span class="text-rose-500 font-bold">*</span>@endif
        </label>
    @endif
    <input {{ $attributes->merge(['class' => 'w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-slate-800 transition duration-150 outline-none placeholder:text-slate-400 disabled:bg-slate-50 disabled:text-slate-500 ' . ($error ? 'border-rose-300 bg-rose-50/20 text-rose-900 focus:border-rose-500 focus:ring-3 focus:ring-rose-500/15' : 'border-slate-300 hover:border-slate-400 focus:border-emerald-600 focus:ring-3 focus:ring-emerald-600/15')]) }} />
    @if($error)
        <p class="mt-1.5 flex items-center gap-1 text-xs font-medium text-rose-600">
            <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>{{ $error }}</span>
        </p>
    @endif
    @if($hint && !$error)
        <p class="mt-1 text-xs text-slate-400">{{ $hint }}</p>
    @endif
</div>
