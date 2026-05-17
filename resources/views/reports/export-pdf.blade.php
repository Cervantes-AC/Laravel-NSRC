<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'NSRC Report' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; }
        header { border-bottom: 2px solid #002147; padding-bottom: 8px; margin-bottom: 16px; }
        h1 { color: #002147; font-size: 18px; margin: 0; }
        .meta { color: #6b7280; font-size: 10px; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #e5e7eb; padding: 6px 8px; text-align: left; }
        th { background: #f3f4f6; }
        footer { position: fixed; bottom: 0; width: 100%; font-size: 9px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <header style="display: flex; align-items: center; gap: 12px;">
        <img src="{{ public_path(config('app.logo', 'images/nsrc-logo.png')) }}" alt="NSRC" style="height: 48px; width: 48px;">
        <div>
            <h1>{{ $appName ?? config('app.name') }} — {{ $title ?? 'Report' }}</h1>
            <p class="meta">Generated {{ $generatedAt ?? now()->format('F j, Y g:i A') }}</p>
        </div>
    </header>

    @if(isset($report))
        <p><strong>{{ __('Type') }}:</strong> {{ $report['type'] ?? '' }}</p>
        <p><strong>{{ __('Generated') }}:</strong> {{ $report['meta']['generated_at'] ?? '' }}</p>
    @endif

    <table>
        <thead>
            <tr>
                @if(!empty($rows) && $rows->isNotEmpty())
                    @foreach(array_keys($rows->first() instanceof \Illuminate\Support\Collection ? $rows->first()->toArray() : (array) $rows->first()) as $col)
                        <th>{{ ucwords(str_replace('_', ' ', $col)) }}</th>
                    @endforeach
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($rows ?? [] as $row)
                <tr>
                    @foreach((array) ($row instanceof \Illuminate\Support\Collection ? $row->toArray() : $row) as $cell)
                        <td>{{ is_array($cell) ? json_encode($cell) : $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <footer>{{ __('Page') }} — {{ $appName ?? 'NSRC AMS' }}</footer>
</body>
</html>
