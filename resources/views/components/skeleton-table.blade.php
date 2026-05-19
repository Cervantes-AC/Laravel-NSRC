@props(['rows' => 5, 'columns' => 5])

<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm']) }}>
    <div class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/90 px-6 py-3">
        <div class="flex gap-8">
            @for($c = 0; $c < $columns; $c++)
                <div class="h-3 w-20 bg-slate-200 dark:bg-slate-700 rounded animate-pulse"></div>
            @endfor
        </div>
    </div>
    @for($r = 0; $r < $rows; $r++)
        <div class="border-b border-slate-100 dark:border-slate-700/50 px-6 py-4">
            <div class="flex gap-8">
                <div class="h-4 w-8 bg-slate-200 dark:bg-slate-700 rounded animate-pulse"></div>
                <div class="h-4 w-32 bg-slate-200 dark:bg-slate-700 rounded animate-pulse"></div>
                <div class="h-4 w-48 bg-slate-200 dark:bg-slate-700 rounded animate-pulse"></div>
                <div class="h-4 w-20 bg-slate-200 dark:bg-slate-700 rounded animate-pulse"></div>
                <div class="h-4 w-24 bg-slate-200 dark:bg-slate-700 rounded animate-pulse"></div>
            </div>
        </div>
    @endfor
</div>
