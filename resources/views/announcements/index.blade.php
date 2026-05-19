<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Announcements') }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if($announcements->count() > 0)
                <div class="space-y-4">
                    @foreach($announcements as $announcement)
                        <div class="bg-white rounded-lg border p-4 @if($announcement->priority === 'urgent') border-l-4 border-l-red-500 @elseif($announcement->priority === 'important') border-l-4 border-l-amber-500 @endif">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        @if($announcement->priority === 'urgent')
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800">URGENT</span>
                                        @elseif($announcement->priority === 'important')
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">IMPORTANT</span>
                                        @endif
                                        <h4 class="text-sm font-semibold text-gray-900">{{ $announcement->title }}</h4>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1 whitespace-pre-line">{{ $announcement->body }}</p>
                                    <p class="text-xs text-gray-400 mt-2">{{ $announcement->published_at?->format('M d, Y h:i A') }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    {{ $announcements->links() }}
                </div>
            @else
                <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-6 py-16 text-center">
                    <div class="w-16 h-16 mx-auto bg-slate-100 rounded-2xl flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-slate-700">No announcements yet</p>
                    <p class="mt-1 text-xs text-slate-500">Published announcements will appear here.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
