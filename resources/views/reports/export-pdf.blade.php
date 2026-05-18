<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Attendance Report' }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            color: #111827;
            line-height: 1.5;
            background: #fff;
        }

        /* â”€â”€ Page layout â”€â”€ */
        .page-wrap { padding: 28px 32px; }

        /* â”€â”€ Header â”€â”€ */
        .header {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding-bottom: 14px;
            border-bottom: 3px solid #1e3a8a;
            margin-bottom: 16px;
        }
        .header-logo {
            width: 56px;
            height: 56px;
            flex-shrink: 0;
            object-fit: contain;
        }
        .header-center { flex: 1; }
        .header-org {
            font-size: 8px;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 2px;
        }
        .header-title {
            font-size: 17px;
            font-weight: 900;
            color: #1e3a8a;
            letter-spacing: -.01em;
            text-transform: uppercase;
        }
        .header-subtitle {
            font-size: 9.5px;
            color: #374151;
            margin-top: 2px;
            font-weight: 500;
        }
        .header-right {
            text-align: right;
            flex-shrink: 0;
        }
        .header-right .generated-label {
            font-size: 7px;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: #9ca3af;
            font-weight: 700;
        }
        .header-right .generated-value {
            font-size: 9.5px;
            font-weight: 700;
            color: #374151;
            margin-top: 2px;
        }
        .header-right .report-code {
            font-size: 7px;
            color: #9ca3af;
            margin-top: 4px;
            font-family: monospace;
        }

        /* â”€â”€ Date range bar â”€â”€ */
        .date-range-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
            padding: 8px 12px;
            background: #f0f4ff;
            border: 1px solid #dbe4ff;
            border-radius: 6px;
            font-size: 8.5px;
        }
        .date-range-bar .drb-label {
            font-weight: 700;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: .06em;
        }
        .date-range-bar .drb-value {
            color: #374151;
            font-weight: 600;
        }

        /* â”€â”€ Summary bar â”€â”€ */
        .summary-bar {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 16px;
        }
        .summary-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px 12px;
            background: #f9fafb;
            text-align: center;
        }
        .summary-card .sc-label {
            font-size: 7px;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: #6b7280;
            font-weight: 700;
        }
        .summary-card .sc-value {
            font-size: 18px;
            font-weight: 900;
            color: #111827;
            margin-top: 3px;
            line-height: 1;
        }
        .summary-card.blue  .sc-value { color: #1d4ed8; }
        .summary-card.green .sc-value { color: #15803d; }
        .summary-card.amber .sc-value { color: #b45309; }
        .summary-card.violet .sc-value { color: #6d28d9; }

        /* â”€â”€ Sector breakdown â”€â”€ */
        .sector-breakdown {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
            padding: 10px 14px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 8px;
        }
        .sector-breakdown .sb-title {
            font-weight: 800;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: 6px;
            font-size: 7.5px;
        }
        .sector-breakdown .sb-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .sector-breakdown .sb-item {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 999px;
        }
        .sector-breakdown .sb-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #1e3a8a;
        }
        .sector-breakdown .sb-count {
            font-weight: 800;
            color: #111827;
        }
        .sector-breakdown .sb-name {
            color: #6b7280;
        }

        /* â”€â”€ Filter pills â”€â”€ */
        .filter-row {
            margin-bottom: 14px;
            font-size: 8.5px;
            color: #6b7280;
            line-height: 1.6;
        }
        .filter-row strong { color: #374151; font-weight: 800; }
        .pill {
            display: inline-block;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            border-radius: 999px;
            padding: 2px 8px;
            margin: 0 2px;
            font-weight: 700;
            font-size: 8px;
        }

        /* â”€â”€ Table â”€â”€ */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            margin-top: 4px;
        }
        table.data-table thead tr {
            background: #1e3a8a;
        }
        table.data-table thead th {
            color: #fff;
            font-weight: 700;
            font-size: 7.5px;
            text-transform: uppercase;
            letter-spacing: .08em;
            padding: 7px 8px;
            text-align: left;
            border: none;
        }
        table.data-table tbody tr:nth-child(even) { background: #f8faff; }
        table.data-table tbody tr:nth-child(odd)  { background: #fff; }
        table.data-table tbody tr:hover { background: #eff6ff; }
        table.data-table tbody td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
            color: #374151;
            vertical-align: middle;
        }
        table.data-table tbody tr:last-child td { border-bottom: none; }

        /* status badges */
        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 999px;
            font-size: 7.5px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
        }
        .badge-complete  { background: #dcfce7; color: #15803d; }
        .badge-ongoing   { background: #dbeafe; color: #1d4ed8; }
        .badge-missing   { background: #fef3c7; color: #b45309; }
        .badge-invalid   { background: #fee2e2; color: #b91c1c; }
        .badge-default   { background: #f3f4f6; color: #374151; }

        .empty-cell {
            text-align: center;
            padding: 28px;
            color: #9ca3af;
            font-style: italic;
        }

        /* â”€â”€ Summary stats table â”€â”€ */
        .stats-table {
            width: 100%;
            margin-top: 18px;
            border-collapse: collapse;
            font-size: 8.5px;
        }
        .stats-table td {
            padding: 6px 10px;
            border: none;
        }
        .stats-table .st-label {
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .06em;
            font-size: 7.5px;
        }
        .stats-table .st-value {
            font-weight: 900;
            color: #111827;
            text-align: right;
        }

        /* â”€â”€ Signature section â”€â”€ */
        .sig-table {
            width: 100%;
            margin-top: 36px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .sig-table td {
            width: 33.33%;
            padding: 0 10px;
            vertical-align: bottom;
            border: none;
        }
        .sig-line {
            border-top: 1.5px solid #374151;
            padding-top: 6px;
            margin-top: 36px;
            text-align: center;
        }
        .sig-role {
            font-size: 7.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #6b7280;
        }
        .sig-name {
            font-size: 9px;
            font-weight: 800;
            color: #111827;
            margin-top: 2px;
        }
        .sig-date {
            font-size: 7.5px;
            color: #9ca3af;
            margin-top: 2px;
        }

        /* â”€â”€ Footer â”€â”€ */
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 6px 32px;
            font-size: 7.5px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        footer .confidential {
            font-weight: 800;
            color: #dc2626;
            text-transform: uppercase;
            letter-spacing: .06em;
        }
        footer .page-num {
            font-family: monospace;
            color: #9ca3af;
        }

        /* â”€â”€ Watermark â”€â”€ */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 72px;
            font-weight: 900;
            color: rgba(30, 58, 138, 0.03);
            text-transform: uppercase;
            letter-spacing: .1em;
            pointer-events: none;
            z-index: 0;
        }
    </style>
</head>
<body>
@php
    $rowCollection = collect($rows ?? ($report['data'] ?? []));
    $totalDuration = $rowCollection->sum(fn($r) =>
        is_array($r) ? ($r['duration_minutes'] ?? 0) : ($r->duration_minutes ?? 0)
    );
    $totalHours   = floor($totalDuration / 60);
    $totalMins    = $totalDuration % 60;
    $totalRecords = $report['meta']['total_records'] ?? $rowCollection->count();
    $avgDuration  = $totalRecords > 0 ? round($totalDuration / $totalRecords) : 0;

    $completeCount = $rowCollection->filter(fn($r) =>
        strtoupper(is_array($r) ? ($r['status'] ?? '') : ($r->status ?? '')) === 'COMPLETE'
    )->count();
    $ongoingCount = $rowCollection->filter(fn($r) =>
        strtoupper(is_array($r) ? ($r['status'] ?? '') : ($r->status ?? '')) === 'ONGOING'
    )->count();
    $missingCount = $rowCollection->filter(fn($r) =>
        strtoupper(is_array($r) ? ($r['status'] ?? '') : ($r->status ?? '')) === 'MISSING_TIMEOUT'
    )->count();
    $completionRate = $totalRecords > 0 ? round(($completeCount / $totalRecords) * 100) : 0;

    $sectorBreakdown = $rowCollection->groupBy(fn($r) =>
        is_array($r) ? ($r['sector'] ?? 'Unassigned') : ($r->sector ?? 'Unassigned')
    )->map(fn($group) => $group->count())->sortDesc();

    $reportCode = 'RPT-' . strtoupper(substr(md5($generatedAt ?? now()->toDateTimeString()), 0, 8));

    $firstRow = $rowCollection->first();
    $firstArray = $firstRow
        ? ($firstRow instanceof \Illuminate\Database\Eloquent\Model
            ? $firstRow->toArray()
            : (array) $firstRow)
        : [];

    $colLabels = [
        'full_name'        => 'Name',
        'date'             => 'Date',
        'time_in'          => 'Time In',
        'time_out'         => 'Time Out',
        'duration_minutes' => 'Duration',
        'location'         => 'Location',
        'sector'           => 'Sector',
        'status'           => 'Status',
    ];

    $badgeClass = fn(string $s) => match(strtoupper($s)) {
        'COMPLETE', 'COMPLETED' => 'badge-complete',
        'ONGOING'               => 'badge-ongoing',
        'MISSING_TIMEOUT'       => 'badge-missing',
        'INVALID_LOG'           => 'badge-invalid',
        default                 => 'badge-default',
    };
@endphp

<div class="page-wrap">

    {{-- Watermark --}}
    <div class="watermark">CONFIDENTIAL</div>

    {{-- Header --}}
    <div class="header">
        @if(file_exists(public_path(config('app.logo', 'images/nsrc-logo.png'))))
            <img src="{{ public_path(config('app.logo', 'images/nsrc-logo.png')) }}"
                 alt="Logo" class="header-logo">
        @endif
        <div class="header-center">
            <div class="header-org">Republic of the Philippines</div>
            <div class="header-org" style="margin-top: 1px;">National Service Reserve Corps</div>
            <div class="header-title">{{ $title ?? 'Attendance Report' }}</div>
            <div class="header-subtitle">Official Records â€” Generated for Management Review</div>
        </div>
        <div class="header-right">
            <div class="generated-label">Generated</div>
            <div class="generated-value">{{ $generatedAt ?? now()->format('F j, Y g:i A') }}</div>
            <div class="report-code">{{ $reportCode }}</div>
        </div>
    </div>

    {{-- Date range --}}
    @if(!empty($dateFrom) || !empty($dateTo))
    <div class="date-range-bar">
        <span class="drb-label">Period:</span>
        <span class="drb-value">{{ $dateFrom ?? 'Start' }} â†’ {{ $dateTo ?? 'Present' }}</span>
    </div>
    @endif

    {{-- Purpose / Scope --}}
    <div style="margin-bottom: 16px; padding: 10px 14px; background: #fff; border-left: 3px solid #1e3a8a; font-size: 8.5px; color: #374151; line-height: 1.6;">
        <strong style="color: #1e3a8a; text-transform: uppercase; letter-spacing: .06em; font-size: 7.5px;">Purpose:</strong>
        This report presents an official record of volunteer attendance and duty sessions for the National Service Reserve Corps (NSRC).
        It is intended for management review, operational planning, and compliance documentation.
    </div>

    {{-- Summary bar --}}
    <div style="margin-bottom: 14px; font-size: 9px;">
        <div style="font-weight: 800; color: #1e3a8a; text-transform: uppercase; letter-spacing: .08em; font-size: 8px; margin-bottom: 8px; padding-bottom: 4px; border-bottom: 2px solid #1e3a8a;">
            Table of Contents
        </div>
        <table style="width: 100%; font-size: 8.5px; border-collapse: collapse;">
            <tr>
                <td style="padding: 3px 0; color: #374151;">I.</td>
                <td style="padding: 3px 0; color: #374151; font-weight: 600;">Executive Summary</td>
                <td style="padding: 3px 0; color: #9ca3af; text-align: right;">Page 1</td>
            </tr>
            <tr>
                <td style="padding: 3px 0; color: #374151;">II.</td>
                <td style="padding: 3px 0; color: #374151; font-weight: 600;">Detailed Attendance Records</td>
                <td style="padding: 3px 0; color: #9ca3af; text-align: right;">Page 1</td>
            </tr>
            <tr>
                <td style="padding: 3px 0; color: #374151;">III.</td>
                <td style="padding: 3px 0; color: #374151; font-weight: 600;">Statistical Summary</td>
                <td style="padding: 3px 0; color: #9ca3af; text-align: right;">Page 2</td>
            </tr>
            <tr>
                <td style="padding: 3px 0; color: #374151;">IV.</td>
                <td style="padding: 3px 0; color: #374151; font-weight: 600;">Certification & Signatures</td>
                <td style="padding: 3px 0; color: #9ca3af; text-align: right;">Page 2</td>
            </tr>
        </table>
    </div>

    {{-- Summary bar --}}
    <div style="margin-bottom: 14px; font-size: 9px;">
        <div style="font-weight: 800; color: #1e3a8a; text-transform: uppercase; letter-spacing: .08em; font-size: 8px; margin-bottom: 8px; padding-bottom: 4px; border-bottom: 2px solid #1e3a8a;">
            I. Executive Summary
        </div>
    </div>
    <div class="summary-bar">
        <div class="summary-card">
            <div class="sc-label">Total Records</div>
            <div class="sc-value">{{ number_format($totalRecords) }}</div>
        </div>
        <div class="summary-card blue">
            <div class="sc-label">Total Hours</div>
            <div class="sc-value">{{ $totalHours }}h {{ sprintf('%02d', $totalMins) }}m</div>
        </div>
        <div class="summary-card green">
            <div class="sc-label">Completion Rate</div>
            <div class="sc-value">{{ $completionRate }}%</div>
        </div>
        <div class="summary-card amber">
            <div class="sc-label">Avg Duration</div>
            <div class="sc-value">{{ $avgDuration }}m</div>
        </div>
    </div>

    {{-- Sector breakdown --}}
    @if($sectorBreakdown->isNotEmpty())
    <div class="sector-breakdown">
        <div style="width: 100%;">
            <div class="sb-title">Sessions by Sector</div>
            <div class="sb-grid">
                @foreach($sectorBreakdown->take(6) as $sector => $count)
                <div class="sb-item">
                    <span class="sb-dot"></span>
                    <span class="sb-count">{{ $count }}</span>
                    <span class="sb-name">{{ $sector !== 'Unassigned' ? $sector : 'â€”' }}</span>
                </div>
                @endforeach
                @if($sectorBreakdown->count() > 6)
                <div class="sb-item">
                    <span class="sb-count" style="color: #6b7280;">+{{ $sectorBreakdown->count() - 6 }} more</span>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Applied filters --}}
    @php
        $appliedFilters = array_filter([
            'Type' => $report['type'] ?? ($title ?? null),
            'Generated' => $generatedAt ?? now()->format('F j, Y'),
            'Records' => number_format($totalRecords),
            'Complete' => number_format($completeCount),
            'Ongoing' => number_format($ongoingCount),
            'Missing' => number_format($missingCount),
        ]);
    @endphp
    <div class="filter-row">
        <strong>Report Summary:</strong>
        @foreach($appliedFilters as $label => $value)
            <span class="pill">{{ $label }}: {{ $value }}</span>
        @endforeach
    </div>

    {{-- Data table --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 22px;">#</th>
                @foreach(array_keys($firstArray) as $col)
                    <th>{{ $colLabels[$col] ?? ucwords(str_replace('_', ' ', $col)) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rowCollection as $i => $row)
                @php
                    $rowArray = $row instanceof \Illuminate\Database\Eloquent\Model
                        ? $row->toArray()
                        : (array) $row;
                @endphp
                <tr>
                    <td style="color: #9ca3af; font-size: 8.5px; font-weight: 600;">{{ $i + 1 }}</td>
                    @foreach($rowArray as $col => $cell)
                        <td>
                            @if($col === 'status' && $cell)
                                <span class="badge {{ $badgeClass((string) $cell) }}">
                                    {{ str_replace('_', ' ', ucfirst(strtolower((string) $cell))) }}
                                </span>
                            @elseif($col === 'duration_minutes' && is_numeric($cell))
                                {{ $cell > 0 ? $cell . ' min' : 'â€”' }}
                            @elseif($col === 'date' && $cell)
                                {{ is_string($cell) && strlen($cell) > 10 ? substr($cell, 0, 10) : $cell }}
                            @elseif(is_array($cell) || is_object($cell))
                                {{ json_encode($cell) }}
                            @else
                                {{ $cell ?? 'â€”' }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($firstArray) + 1 }}" class="empty-cell">
                        No records available for the selected filters.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Bottom stats --}}
    @if( > 0)
    <div style="margin-bottom: 14px; margin-top: 20px; font-size: 9px;">
        <div style="font-weight: 800; color: #1e3a8a; text-transform: uppercase; letter-spacing: .08em; font-size: 8px; margin-bottom: 8px; padding-bottom: 4px; border-bottom: 2px solid #1e3a8a;">
            III. Statistical Summary
        </div>
    </div>
    <table class="stats-table">
        <tr>
            <td class="st-label">Total Duration</td>
            <td class="st-value">{{ $totalHours }}h {{ sprintf('%02d', $totalMins) }}m</td>
            <td class="st-label">Completed</td>
            <td class="st-value">{{ number_format($completeCount) }}</td>
            <td class="st-label">Ongoing</td>
            <td class="st-value">{{ number_format($ongoingCount) }}</td>
        </tr>
        <tr>
            <td class="st-label">Missing Timeout</td>
            <td class="st-value">{{ number_format($missingCount) }}</td>
            <td class="st-label">Avg / Session</td>
            <td class="st-value">{{ $avgDuration }} min</td>
            <td class="st-label">Report Code</td>
            <td class="st-value" style="font-family: monospace; font-size: 8px;">{{ $reportCode }}</td>
        </tr>
    </table>
    @endif

    {{-- Certification statement --}}
    <div style="margin-top: 24px; margin-bottom: 16px; padding: 12px 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-left: 3px solid #1e3a8a; font-size: 8px; color: #374151; line-height: 1.7; page-break-inside: avoid;">
        <strong style="color: #1e3a8a; text-transform: uppercase; letter-spacing: .06em; font-size: 7.5px;">Certification:</strong><br>
        I hereby certify that the data presented in this report is true and correct to the best of my knowledge and belief,
        based on the official records maintained by the NSRC Attendance Management System.<br><br>
        This report is generated electronically and is valid without wet signature unless otherwise required by existing laws and regulations.
    </div>

    {{-- Signature block --}}
    <div style="margin-bottom: 14px; margin-top: 20px; font-size: 9px;">
        <div style="font-weight: 800; color: #1e3a8a; text-transform: uppercase; letter-spacing: .08em; font-size: 8px; margin-bottom: 8px; padding-bottom: 4px; border-bottom: 2px solid #1e3a8a;">
            IV. Certification & Signatures
        </div>
    </div>
    <table class="sig-table">
        <tr>
            <td>
                <div class="sig-line">
                    <div class="sig-role">Prepared by</div>
                    <div class="sig-name">&nbsp;</div>
                    <div class="sig-date">Date: _______________</div>
                </div>
            </td>
            <td>
                <div class="sig-line">
                    <div class="sig-role">Reviewed by</div>
                    <div class="sig-name">&nbsp;</div>
                    <div class="sig-date">Date: _______________</div>
                </div>
            </td>
            <td>
                <div class="sig-line">
                    <div class="sig-role">Approved by</div>
                    <div class="sig-name">&nbsp;</div>
                    <div class="sig-date">Date: _______________</div>
                </div>
            </td>
        </tr>
    </table>

</div>

<footer>
    <span>{{ $appName ?? 'NSRC AMS' }} Â· Confidential â€” Do Not Distribute</span>
    <span class="confidential">Confidential</span>
    <span class="page-num">Page {PAGE_NUM} of {PAGE_COUNT}</span>
</footer>
</body>
</html>



