<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Email Notification Preferences') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Choose which email notifications you would like to receive.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.email-notifications') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="flex items-center gap-3">
            <input
                id="email_notifications"
                name="email_notifications"
                type="checkbox"
                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                value="1"
                @checked(auth()->user()->email_notifications_enabled ?? true)
            >
            <label for="email_notifications" class="text-sm font-medium text-gray-700">
                {{ __('Receive email notifications') }}
            </label>
        </div>

        <div class="ml-6 space-y-2 text-sm text-gray-600">
            <p class="text-xs text-gray-500">When enabled, you will receive emails for:</p>
            <ul class="list-disc list-inside text-xs text-gray-500 space-y-1">
                <li>Welcome email upon registration</li>
                <li>Account approval or rejection</li>
                <li>Duty session time-in and time-out confirmations</li>
                <li>New announcements</li>
                <li>Backup and import/export notifications (admin only)</li>
            </ul>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'notification-preferences-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
