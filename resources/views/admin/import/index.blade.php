<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Import Data') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('success'))
                <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800" role="status">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('warning'))
                <div class="rounded-lg bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-800" role="alert">
                    {{ session('warning') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Upload File') }}</h3>
                            <form method="POST" action="{{ route('admin.import.process') }}" enctype="multipart/form-data" class="space-y-6">
                                @csrf

                                <div>
                                    <label for="import_type" class="block text-sm font-medium text-gray-700">{{ __('Import Type') }}</label>
                                    <select id="import_type" name="import_type" required aria-required="true" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">{{ __('Select Type') }}</option>
                                        <option value="personnel" {{ old('import_type') === 'personnel' ? 'selected' : '' }}>{{ __('Personnel') }}</option>
                                        <option value="sessions" {{ old('import_type') === 'sessions' ? 'selected' : '' }}>{{ __('Duty Sessions') }}</option>
                                    </select>
                                    @error('import_type')
                                        <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-indigo-500 transition">
                                    <label for="file" class="cursor-pointer">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <span class="mt-2 block text-sm font-medium text-gray-700">{{ __('Click to upload or drag and drop') }}</span>
                                        <span class="mt-1 block text-xs text-gray-500">{{ __('CSV, XLSX, TXT files up to 10MB') }}</span>
                                        <input id="file" name="file" type="file" accept=".csv,.xlsx,.xls,.txt" class="hidden" aria-label="{{ __('Choose file to import') }}" />
                                    </label>
                                    @error('file')
                                        <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div id="file-preview" class="hidden">
                                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                        <svg class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div>
                                            <p id="file-name" class="text-sm font-medium text-gray-900"></p>
                                            <p id="file-size" class="text-xs text-gray-500"></p>
                                        </div>
                                        <button type="button" id="remove-file" class="ml-auto text-sm text-red-600 hover:text-red-800" aria-label="{{ __('Remove file') }}">{{ __('Remove') }}</button>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="send_email" value="1" checked class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <span class="text-sm text-gray-700">{{ __('Send email notification') }}</span>
                                    </label>
                                </div>

                                <div id="import-progress" class="hidden">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Import Progress') }}</label>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                        <div id="progress-bar" class="bg-indigo-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                                    </div>
                                    <p id="progress-text" class="mt-1 text-xs text-gray-500">{{ __('Starting...') }}</p>
                                </div>

                                <div class="flex items-center gap-4">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition" aria-label="{{ __('Import file') }}">
                                        {{ __('Import') }}
                                    </button>
                                    <button type="button" id="preview-btn" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition" aria-label="{{ __('Preview import data') }}">
                                        {{ __('Preview') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Export Data') }}</h3>
                            <p class="text-sm text-gray-600 mb-4">{{ __('Download data as CSV, Excel, or PDF:') }}</p>
                            <div class="space-y-3">
                                <a href="{{ route('admin.export.sessions') }}" class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition" aria-label="{{ __('Export sessions') }}">
                                    <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700">{{ __('Export Sessions') }}</span>
                                </a>
                                <a href="{{ route('admin.export.personnel') }}" class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition" aria-label="{{ __('Export personnel') }}">
                                    <svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700">{{ __('Export Personnel') }}</span>
                                </a>
                                <a href="{{ route('admin.export.accounts') }}" class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition" aria-label="{{ __('Export accounts') }}">
                                    <svg class="h-6 w-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700">{{ __('Export Accounts') }}</span>
                                </a>
                                <a href="{{ route('admin.export.attendance') }}" class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition" aria-label="{{ __('Export attendance') }}">
                                    <svg class="h-6 w-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700">{{ __('Export Attendance') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Templates') }}</h3>
                            <p class="text-sm text-gray-600 mb-4">{{ __('Download a template to ensure correct formatting:') }}</p>
                            <div class="space-y-3">
                                <a href="{{ route('admin.import.template', 'personnel') }}" class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition" aria-label="{{ __('Download personnel template') }}">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700">{{ __('Personnel Template') }}</span>
                                </a>
                                <a href="{{ route('admin.import.template') }}" class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition" aria-label="{{ __('Download attendance template') }}">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700">{{ __('Attendance Template') }}</span>
                                </a>
                            </div>

                            <div class="mt-6 p-3 bg-blue-50 rounded-lg">
                                <h4 class="text-sm font-medium text-blue-800 mb-2">{{ __('Requirements') }}</h4>
                                <ul class="text-xs text-blue-700 space-y-1 list-disc list-inside">
                                    <li>{{ __('Max file size: 10MB') }}</li>
                                    <li>{{ __('Supported formats: CSV, XLSX, XLS, TXT') }}</li>
                                    <li>{{ __('Required: timestamp, full_name, attendance') }}</li>
                                    <li>{{ __('Attendance values: Time in, Time out') }}</li>
                                    <li>{{ __('Email notification sent to: aaronclydeccervantes@gmail.com') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
    document.getElementById('file').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            document.getElementById('file-name').textContent = file.name;
            document.getElementById('file-size').textContent = (file.size / 1024).toFixed(1) + ' KB';
            document.getElementById('file-preview').classList.remove('hidden');
        }
    });

    document.getElementById('remove-file').addEventListener('click', function() {
        document.getElementById('file').value = '';
        document.getElementById('file-preview').classList.add('hidden');
    });

    document.getElementById('preview-btn').addEventListener('click', function() {
        const progress = document.getElementById('import-progress');
        progress.classList.remove('hidden');
        let width = 0;
        const bar = document.getElementById('progress-bar');
        const text = document.getElementById('progress-text');
        const interval = setInterval(function() {
            width += 10;
            bar.style.width = width + '%';
            bar.setAttribute('aria-valuenow', width);
            text.textContent = width < 100 ? '{{ __("Processing...") }}' : '{{ __("Preview ready") }}';
            if (width >= 100) clearInterval(interval);
        }, 200);
    });
</script>
@endpush
