@props(['count' => 1])

@for($i = 0; $i < $count; $i++)
    <div {{ $attributes->merge(['class' => 'rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5 shadow-sm']) }}>
        <div class="h-3 w-24 bg-slate-200 dark:bg-slate-700 rounded animate-pulse mb-3"></div>
        <div class="h-8 w-16 bg-slate-200 dark:bg-slate-700 rounded animate-pulse mb-2"></div>
        <div class="h-3 w-32 bg-slate-200 dark:bg-slate-700 rounded animate-pulse"></div>
    </div>
@endfor
