@csrf

<div class="grid gap-6">
    <div>
        <x-input-label for="title" :value="__('Title')" />
        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $announcement->title)" required />
        <x-input-error class="mt-2" :messages="$errors->get('title')" />
    </div>

    <div>
        <x-input-label for="body" :value="__('Message')" />
        <textarea id="body" name="body" rows="7" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('body', $announcement->body) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('body')" />
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <x-input-label for="priority" :value="__('Priority')" />
            <select id="priority" name="priority" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @foreach (['normal' => 'Normal', 'important' => 'Important', 'urgent' => 'Urgent'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('priority', $announcement->priority) === $value)>{{ __($label) }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('priority')" />
        </div>

        <div>
            <x-input-label for="status" :value="__('Status')" />
            <select id="status" name="status" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @foreach (['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $announcement->status) === $value)>{{ __($label) }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('status')" />
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <x-input-label for="audience" :value="__('Audience')" />
            <select id="audience" name="audience" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="members" @selected(old('audience', $announcement->audience) === 'members')>{{ __('Members') }}</option>
                <option value="all" @selected(old('audience', $announcement->audience) === 'all')>{{ __('All Users') }}</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('audience')" />
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.announcements.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">{{ __('Cancel') }}</a>
        <x-primary-button>{{ __('Save Announcement') }}</x-primary-button>
    </div>
</div>
