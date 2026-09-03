@props(['title' => ''])
<span class="relative inline-flex" x-data>
    @if($count > 0)
        <span class="absolute -right-1 -top-1 grid h-4.5 min-w-4.5 place-items-center rounded-full bg-rose-600 px-1 text-[10px] font-bold text-white ring-2 ring-white">
            {{ $count > 99 ? '99+' : $count }}
        </span>
    @endif
</span>
