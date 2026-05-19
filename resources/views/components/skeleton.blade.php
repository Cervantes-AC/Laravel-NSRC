@props(['class' => '', 'lines' => 1, 'width' => '100%'])

@if($lines > 1)
    <div {{ $attributes->merge(['class' => 'space-y-2 ' . $class]) }}>
        @for($i = 0; $i < $lines; $i++)
            <div class="h-4 bg-slate-200 dark:bg-slate-700 rounded animate-pulse" style="width: {{ $i === $lines - 1 ? '60%' : $width }}"></div>
        @endfor
    </div>
@else
    <div {{ $attributes->merge(['class' => 'h-4 bg-slate-200 dark:bg-slate-700 rounded animate-pulse ' . $class]) }} style="width: {{ $width }}"></div>
@endif
