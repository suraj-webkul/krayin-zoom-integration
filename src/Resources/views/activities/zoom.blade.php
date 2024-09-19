@php
    $account = app('\Webkul\ZoomMeeting\Repositories\AccountRepository')->findOneByField('user_id', auth()->user()->id);
@endphp

<!-- Zoom Meet Activity Vue Component -->
<v-zoom-meet-activity :set-values="setValues"></v-zoom-meet-activity>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-zoom-meet-activity-template"
    >
        <x-admin::form.control-group>
            <div class="flex">
                @if ($account)
                    <template v-if="! meet.hasMeetLocation">
                        <!-- Create Zoom Meet -->
                        <button 
                            type="button"
                            class="secondary-button"
                            @click="create"
                            :disable="isLoading"
                        >
                            <!-- Spinner -->
                            <x-admin::spinner
                                class="h-4 w-4"
                                v-if="isLoading"
                            />

                            <div class="flex items-center gap-2">
                                <img 
                                    src="{{ vite()->asset('images/zoom-logo.png', 'zoom_meeting') }}"
                                    class="h-5 w-5"
                                >
                
                                <p>@lang('zoom_meeting::app.leads.view.activities.create-zoom')</p>
                            </div>
                        </button>
                    </template>

                    <template v-else>
                        <!-- Join Zoom Meet -->
                        <div class="flex items-center gap-2">
                            <a 
                                :href="meet?.location"
                                target="_blank"
                                class="secondary-button"
                            >
                                <div class="flex items-center gap-2">
                                    <img 
                                        src="{{ vite()->asset('images/zoom-logo.png', 'zoom_meeting') }}"
                                        class="h-5 w-5"
                                    >
                    
                                    <p>@lang('zoom_meeting::app.leads.view.activities.join-zoom')</p>
                                </div>
                            </a>

                            <!-- Remove -->
                            <span
                                @click="remove"
                                title="@lang('zoom_meeting::app.activity.remove-zoom-meet')"
                                class="icon-delete cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"
                            ></span>
                        </div>
                    </template>
                @else
                    <!-- Connect to Zoom Meet -->
                    <a 
                        href="{{ route('admin.zoom.index', ['route' => 'meet']) }}"
                        class="secondary-button"
                        @click="create"
                    >
                        <div class="flex items-center gap-2">
                            <img 
                                src="{{ vite()->asset('images/zoom-logo.png', 'zoom_meeting') }}"
                                class="h-5 w-5"
                            >
            
                            <p>@lang('zoom_meeting::app.leads.view.activities.connect')</p>
                        </div>
                    </a>
                @endif
            </div>
        </x-admin::form.control-group>
    </script>

    <script type="module">
        app.component('v-zoom-meet-activity', {
            template: '#v-zoom-meet-activity-template',

            props: ['setValues'],

            data() {
                return {
                    isLoading: false,

                    activity: @json($activity),

                    meet: {
                        hasMeetLocation: false,
                        location: '',
                        comment: '',
                    },
                };
            },

            mounted() {
                if (
                    this.activity.location 
                    && this.activity.location.includes('zoom')
                ) {
                    this.setFormValues({
                        location: this.activity.location,
                        comment: this.activity.comment,
                        hasMeetLocation: true,
                    });
                }
            },

            methods: {
                remove() {
                    this.$emitter.emit('open-confirm-modal', {
                        agree: () => {
                            this.setFormValues({
                                location: '',
                                comment: '',
                            });
                        },
                    });
                },

                setFormValues({ location, comment, hasMeetLocation = false }) {
                    this.meet = {
                        location,
                        comment,
                        hasMeetLocation
                    };

                    this.setValues({
                        location,
                        comment,
                    });
                },

                create(event) {                    
                    this.isLoading = true;
                    
                    let formData = new FormData(event.target.closest('form'));

                    this.$axios.post('{{ route('admin.zoom_meeting.create_link') }}', {
                        'title': formData.get('title'),
                        'schedule_from': formData.get('schedule_from'),
                        'schedule_to': formData.get('schedule_to'),
                        participants: {
                            users: formData.getAll('participants[users][]'),
                            persons: formData.getAll('participants[persons][]'),
                        },
                    })
                        .then(response => {
                            this.setFormValues({
                                location: response.data.link,
                                comment: response.data.comment,
                                hasMeetLocation: true,
                            });
                        })
                        .catch(error => {
                            this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });
                        })
                        .finally(() => {
                            this.isLoading = false;
                        });
                },
            },
        });
    </script>
@endPushOnce
