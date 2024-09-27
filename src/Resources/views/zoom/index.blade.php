<x-admin::layouts>
    <!-- Title -->
    <x-slot:title>
        @lang('zoom_meeting::app.zoom.index.title')
    </x-slot>

    <!-- Body -->
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="flex flex-col gap-2">
                <div class="flex cursor-pointer items-center">
                    <x-admin::breadcrumbs name="zoom.meeting.create" />
                </div>

                <div class="text-xl font-bold dark:text-white">
                    @lang('zoom_meeting::app.zoom.index.title')
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="box-shadow flex flex-col gap-4 rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 max-xl:flex-wrap">
            <div class="flex gap-2 border-b border-gray-200 dark:border-gray-800">
                <!-- Tab -->
                <div class="dark:brandColor inline-block cursor-pointer border-b-2 border-brandColor px-3 py-2.5 text-sm font-medium text-brandColor">
                    @lang('zoom_meeting::app.zoom.index.title')
                </div>
            </div>

            <div class="flex flex-col gap-4 px-4 py-2">
                @if ($account)
                    <!-- Remove the account -->
                    <x-admin::form         
                        :action="route('admin.zoom_meeting.destroy', $account->id)"
                        method="DELETE"
                        class="p-4"
                    >
                        <input
                            name="route"
                            type="hidden"
                            value="meet"
                        >

                        <div class="flex gap-2">
                            <img 
                                src="{{ vite()->asset('images/zoom-logo.png', 'zoom_meeting') }}"
                                class="h-10 w-10"
                            >

                            <div class="flex flex-col gap-2">
                                <h1 class="text-1xl font-semibold leading-none dark:text-white">
                                    @lang('zoom_meeting::app.zoom.index.title')
                                </h1>
                                
                                <p class="dark:text-white">@lang('zoom_meeting::app.zoom.index.info')</p>

                                <div class="flex">
                                    <button
                                        type="submit"
                                        class="text-red-500 hover:underline"
                                    >
                                        @lang('zoom_meeting::app.zoom.index.remove')
                                    </button>
                                </div>
                            </div>
                        </div>
                    </x-admin::form>
                @else
                    <!-- Connect to the Zoom -->
                    <div class="flex">
                        <a
                            href="{{ route('admin.zoom_meeting.store') }}"
                            class="secondary-button"
                        >
                            <div class="flex items-center gap-2">
                                <img 
                                    src="{{ vite()->asset('images/zoom-logo.png', 'zoom_meeting') }}"
                                    class="h-5 w-5"
                                >

                                <p>@lang('zoom_meeting::app.zoom.index.connect-zoom-meeting')</p>
                            </div>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin::layouts>