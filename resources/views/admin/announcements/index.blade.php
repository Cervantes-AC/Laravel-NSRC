<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Announcements') }}</h2>
            <a href="{{ route('admin.announcements.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">{{ __('New Announcement') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-6">
                    @if (session('success'))
                        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
                    @endif

                    @if (session('warning'))
                        <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">{{ session('warning') }}</div>
                    @endif

                    <form method="GET" class="grid gap-3 md:grid-cols-[1fr_12rem_10rem_auto]">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search announcements...') }}" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <select name="status" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('All Statuses') }}</option>
                            @foreach (['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'] as $value => $label)
                                <option value="{{ $value }}" @selected(request('status') === $value)>{{ __($label) }}</option>
                            @endforeach
                        </select>
                        <select name="trashed" class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Active Only') }}</option>
                            <option value="with" @selected(request('trashed') === 'with')>{{ __('With Deleted') }}</option>
                            <option value="only" @selected(request('trashed') === 'only')>{{ __('Deleted Only') }}</option>
                        </select>
                        <x-primary-button>{{ __('Filter') }}</x-primary-button>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">{{ __('Title') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">{{ __('Status') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">{{ __('Priority') }}</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">{{ __('Deleted') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-500">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse ($announcements as $announcement)
                                    <tr class="@if($announcement->trashed()) opacity-60 bg-slate-50 @endif">
                                        <td class="px-4 py-4">
                                            <p class="font-semibold text-gray-900">{{ $announcement->title }}</p>
                                            <p class="mt-1 line-clamp-2 text-sm text-gray-500">{{ $announcement->body }}</p>
                                        </td>
                                        <td class="px-4 py-4 text-sm">{{ ucfirst($announcement->status) }}</td>
                                        <td class="px-4 py-4 text-sm">{{ ucfirst($announcement->priority) }}</td>
                                        <td class="px-4 py-4 text-sm text-gray-500">
                                            @if($announcement->trashed())
                                                {{ $announcement->deleted_at->format('Y-m-d H:i') }}
                                            @else
                                                <span class="text-green-600">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-right text-sm">
                                            @if($announcement->trashed())
                                                <a href="{{ route('admin.announcements.restore', $announcement->id) }}" class="font-semibold text-green-600 hover:text-green-800" onclick="return confirm('{{ __('Restore this announcement?') }}')">{{ __('Restore') }}</a>
                                            @else
                                                <a href="{{ route('admin.announcements.edit', $announcement) }}" class="font-semibold text-indigo-600 hover:text-indigo-800">{{ __('Edit') }}</a>
                                                <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}" class="ml-3 inline" onsubmit="return confirm('{{ __('Delete this announcement?') }}')">
                                                    @csrf
                                                    @method('delete')
                                                    <button class="font-semibold text-red-600 hover:text-red-800">{{ __('Delete') }}</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-12 text-center text-sm text-gray-500">{{ __('No announcements found.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $announcements->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
