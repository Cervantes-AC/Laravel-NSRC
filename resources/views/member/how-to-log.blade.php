<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('How to Log Attendance') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="space-y-8">
                        <div class="flex items-start gap-4">
                            <span class="flex items-center justify-center w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 font-bold text-lg flex-shrink-0" aria-hidden="true">1</span>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ __('Log In to Your Account') }}</h3>
                                <p class="mt-1 text-sm text-gray-600">{{ __('Use your registered email and password to log in to the NSRC Attendance Management System. If you don\'t have an account yet, contact your administrator.') }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <span class="flex items-center justify-center w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 font-bold text-lg flex-shrink-0" aria-hidden="true">2</span>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ __('Navigate to Dashboard') }}</h3>
                                <p class="mt-1 text-sm text-gray-600">{{ __('Once logged in, you will see your personal dashboard. This displays your recent activity, stats, and quick actions for logging attendance.') }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <span class="flex items-center justify-center w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 font-bold text-lg flex-shrink-0" aria-hidden="true">3</span>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ __('Click "Log Time In"') }}</h3>
                                <p class="mt-1 text-sm text-gray-600">{{ __('When you arrive for your duty, click the "Log Time In" button on your dashboard. This records the current date and time as your session start.') }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <span class="flex items-center justify-center w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 font-bold text-lg flex-shrink-0" aria-hidden="true">4</span>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ __('Select Location and Sector') }}</h3>
                                <p class="mt-1 text-sm text-gray-600">{{ __('Choose your duty location and sector from the provided options. This helps the system accurately track where and what type of volunteer work you are performing.') }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <span class="flex items-center justify-center w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 font-bold text-lg flex-shrink-0" aria-hidden="true">5</span>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ __('Click "Log Time Out" When Done') }}</h3>
                                <p class="mt-1 text-sm text-gray-600">{{ __('When you finish your duty, click the "Log Time Out" button. The system will automatically calculate your total hours and update your session record.') }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <span class="flex items-center justify-center w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 font-bold text-lg flex-shrink-0" aria-hidden="true">6</span>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ __('Review Your Attendance') }}</h3>
                                <p class="mt-1 text-sm text-gray-600">{{ __('Visit the "My Attendance" page to view all your logged sessions, check your total hours, and verify that all entries are correct.') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-start gap-3">
                            <svg class="h-5 w-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium text-yellow-800">{{ __('Important') }}</h4>
                                <p class="mt-1 text-sm text-yellow-700">{{ __('Always log your time in and time out on the same day. If you encounter any issues, contact the system administrator immediately.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
