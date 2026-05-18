@props([
    'headers' => [],
    'rows' => [],
    'striped' => true,
    'hoverable' => true,
])

<div class="w-full overflow-x-auto">
    {{-- Mobile: Card View (< md) --}}
    <div class="md:hidden space-y-3">
        @forelse($rows as $row)
            <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
                @foreach($headers as $index => $header)
                    @if(isset($row[$index]))
                        <div class="flex justify-between items-start mb-3 last:mb-0">
                            <span class="text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                {{ $header }}
                            </span>
                            <span class="text-sm text-slate-900 font-medium text-right ml-2">
                                {{ $row[$index] }}
                            </span>
                        </div>
                    @endif
                @endforeach
            </div>
        @empty
            <div class="text-center py-8 text-slate-500">
                <p class="text-sm">No data available</p>
            </div>
        @endforelse
    </div>

    {{-- Desktop: Table View (>= md) --}}
    <table class="hidden md:table w-full text-sm">
        <thead>
            <tr class="border-b border-slate-200 bg-slate-50">
                @foreach($headers as $header)
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $rowIndex => $row)
                <tr class="border-b border-slate-200 {{ $hoverable ? 'hover:bg-slate-50 transition' : '' }} {{ $striped && $rowIndex % 2 === 0 ? 'bg-slate-50' : '' }}">
                    @foreach($row as $cell)
                        <td class="px-6 py-4 text-slate-900">
                            {{ $cell }}
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) }}" class="px-6 py-8 text-center text-slate-500">
                        <p class="text-sm">No data available</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
