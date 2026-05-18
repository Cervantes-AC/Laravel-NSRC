<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Formal Attendance Report' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; line-height: 1.45; }
        header { border-bottom: 3px solid #1f2937; padding-bottom: 12px; margin-bottom: 18px; }
        h1 { color: #111827; font-size: 20px; margin: 0; text-transform: uppercase; letter-spacing: .04em; }
        h2 { color: #374151; font-size: 13px; margin: 2px 0 0; font-weight: normal; }
        .meta { color: #4b5563; font-size: 10px; margin-top: 8px; }
        .summary { border: 1px solid #d1d5db; background: #f9fafb; padding: 10px 12px; margin-bottom: 14px; }
        .summary p { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #e5e7eb; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background: #e5e7eb; color: #111827; font-size: 10px; text-transform: uppercase; }
        .empty { padding: 24px; text-align: center; color: #6b7280; }
        .prepared { margin-top: 28px; width: 100%; }
        .prepared td { border: 0; padding-top: 26px; }
        .line { border-top: 1px solid #111827; padding-top: 4px; width: 180px; }
        footer { position: fixed; bottom: 0; width: 100%; font-size: 9px; color: #6b7280; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 6px; }
    </style>
</head>
<body>
    <header style="display: flex; align-items: center; gap: 12px;">
        <img src="{{ public_path(config('app.logo', 'images/nsrc-logo.png')) }}" alt="NSRC" style="height: 48px; width: 48px;">
        <div>
            <h1>{{ $appName ?? config('app.name', 'NSRC Attendance Management System') }}</h1>
            <h2>{{ $title ?? 'Formal Attendance Report' }}</h2>
            <p class="meta">Generated on {{ $generatedAt ?? now()->format('F j, Y g:i A') }}</p>
        </div>
    </header>

    @php
        $rowCollection = collect($rows ?? ($report['data'] ?? []));
        $firstRow = $rowCollection->first();
        $firstArray = $firstRow instanceof \Illuminate\Database\Eloquent\Model
            ? $firstRow->toArray()
            : ($firstRow instanceof \Illuminate\Support\Collection ? $firstRow->toArray() : (array) $firstRow);
    @endphp

    <div class="summary">
        <p><strong>Report Type:</strong> {{ $report['type'] ?? ($title ?? 'Attendance Report') }}</p>
        <p><strong>Total Records:</strong> {{ $report['meta']['total_records'] ?? $rowCollection->count() }}</p>
        <p><strong>Prepared For:</strong> Company records and management review</p>
    </div>

    <table>
        <thead>
            <tr>
                @foreach(array_keys($firstArray) as $col)
                    <th>{{ ucwords(str_replace('_', ' ', $col)) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rowCollection as $row)
                @php
                    $rowArray = $row instanceof \Illuminate\Database\Eloquent\Model
                        ? $row->toArray()
                        : ($row instanceof \Illuminate\Support\Collection ? $row->toArray() : (array) $row);
                @endphp
                <tr>
                    @foreach($rowArray as $cell)
                        <td>
                            @if (is_array($cell))
                                {{ json_encode($cell) }}
                            @elseif (is_object($cell) && method_exists($cell, '__toString'))
                                {{ (string) $cell }}
                            @elseif (is_object($cell))
                                {{ json_encode($cell) }}
                            @else
                                {{ $cell }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td class="empty">No report records are available for the selected filters.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="prepared">
        <tr>
            <td><div class="line">Prepared by</div></td>
            <td><div class="line">Reviewed by</div></td>
            <td><div class="line">Approved by</div></td>
        </tr>
    </table>

    <footer>{{ $appName ?? 'NSRC AMS' }} | Confidential Company Report</footer>
</body>
</html>
