<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Announcements') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if(isset($announcements) && $announcements->hasItems())
                <div class="space-y-3">
                    @foreach($announcements as $announcement)
                        <article class="rounded-lg border {{ $announcement->priority === 'urgent' ? 'border-red-200 bg-red-50' : ($announcement->priority === 'important' ? 'border-amber-200 bg-amber-50' : 'border-blue-200 bg-blue-50') }} p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ __('Announcement') }} · {{ ucfirst($announcement->priority) }}</p>
                                    <h3 class="mt-1 text-base font-semibold text-gray-900">{{ $announcement->title }}</h3>
                                </div>
                                <span class="text-xs text-gray-500">{{ optional($announcement->published_at)->diffForHumans() ?? $announcement->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="mt-3 whitespace-pre-line text-sm text-gray-700">{{ $announcement->body }}</p>
                        </article>
                    @endforeach
                </div>
                <div class="mt-6">
                    {{ $announcements->links() }}
                </div>
            @else
                <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-6 py-16 text-center">
                    <div class="w-16 h-16 mx-auto bg-slate-100 rounded-2xl flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-slate-700">No announcements yet</p>
                    <p class="mt-1 text-xs text-slate-500">Announcements from the admin will appear here.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
