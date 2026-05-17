<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Attendance Policies & Requirements') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="space-y-8">
                        <section>
                            <h3 class="text-lg font-medium text-gray-900 flex items-center gap-2">
                                <svg class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('General Rules') }}
                            </h3>
                            <ul class="mt-3 space-y-2 text-sm text-gray-600">
                                <li class="flex items-start gap-2">
                                    <span class="text-indigo-500 mt-0.5">&bull;</span>
                                    {{ __('All volunteers must log their attendance for every duty session.') }}
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-indigo-500 mt-0.5">&bull;</span>
                                    {{ __('Attendance must be logged on the same day as the duty session.') }}
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-indigo-500 mt-0.5">&bull;</span>
                                    {{ __('Only the assigned volunteer may log their own attendance. Proxy logging is prohibited.') }}
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-indigo-500 mt-0.5">&bull;</span>
                                    {{ __('Volunteers must select the correct location and sector for each session.') }}
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-indigo-500 mt-0.5">&bull;</span>
                                    {{ __('All sessions are subject to verification and audit by administrators.') }}
                                </li>
                            </ul>
                        </section>

                        <section>
                            <h3 class="text-lg font-medium text-gray-900 flex items-center gap-2">
                                <svg class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('Time Requirements') }}
                            </h3>
                            <ul class="mt-3 space-y-2 text-sm text-gray-600">
                                <li class="flex items-start gap-2">
                                    <span class="text-indigo-500 mt-0.5">&bull;</span>
                                    {{ __('Minimum session duration is 30 minutes. Shorter sessions will not be counted.') }}
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-indigo-500 mt-0.5">&bull;</span>
                                    {{ __('Maximum session duration is 8 hours per day.') }}
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-indigo-500 mt-0.5">&bull;</span>
                                    {{ __('Volunteers must log time out before 11:59 PM on the same day.') }}
                                </li>
                            </ul>
                        </section>

                        <section>
                            <h3 class="text-lg font-medium text-gray-900 flex items-center gap-2">
                                <svg class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01" />
                                </svg>
                                {{ __('Integrity & Compliance') }}
                            </h3>
                            <ul class="mt-3 space-y-2 text-sm text-gray-600">
                                <li class="flex items-start gap-2">
                                    <span class="text-indigo-500 mt-0.5">&bull;</span>
                                    {{ __('The system monitors for anomalies such as impossible time entries (e.g., time out before time in).') }}
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-indigo-500 mt-0.5">&bull;</span>
                                    {{ __('Repeated integrity violations may result in account suspension.') }}
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-indigo-500 mt-0.5">&bull;</span>
                                    {{ __('Administrators reserve the right to modify or void any session record found to be in violation of policies.') }}
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-indigo-500 mt-0.5">&bull;</span>
                                    {{ __('All attendance data is logged and stored for audit purposes.') }}
                                </li>
                            </ul>
                        </section>

                        <section>
                            <h3 class="text-lg font-medium text-gray-900 flex items-center gap-2">
                                <svg class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('Support') }}
                            </h3>
                            <ul class="mt-3 space-y-2 text-sm text-gray-600">
                                <li class="flex items-start gap-2">
                                    <span class="text-indigo-500 mt-0.5">&bull;</span>
                                    {{ __('For technical issues, contact the system administrator.') }}
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-indigo-500 mt-0.5">&bull;</span>
                                    {{ __('For policy clarifications, refer to the NSRC Volunteer Handbook.') }}
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-indigo-500 mt-0.5">&bull;</span>
                                    {{ __('Report any suspicious activity or unauthorized access immediately.') }}
                                </li>
                            </ul>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
