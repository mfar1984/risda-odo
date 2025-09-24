<x-dashboard-layout title="Komponen">
    <x-ui.page-header
        title="Komponen"
        description="Showcase semua UI components dengan navigation tabs"
    >
        <!-- Tab Navigation -->
        <div class="mb-8" x-data="{ activeTab: 'tabs' }">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Tabs">
                    <button @click="activeTab = 'tabs'"
                            :class="activeTab === 'tabs' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'tabs' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">tab</span>
                        Tabs
                    </button>
                    <button @click="activeTab = 'accordions'"
                            :class="activeTab === 'accordions' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'accordions' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">expand_more</span>
                        Accordions
                    </button>
                    <button @click="activeTab = 'notifications'"
                            :class="activeTab === 'notifications' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'notifications' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">notifications</span>
                        Notifications
                    </button>
                    <button @click="activeTab = 'modals'"
                            :class="activeTab === 'modals' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'modals' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">web_asset</span>
                        Modals
                    </button>
                    <button @click="activeTab = 'loading'"
                            :class="activeTab === 'loading' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'loading' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">hourglass_empty</span>
                        Loading
                    </button>
                    <button @click="activeTab = 'progress'"
                            :class="activeTab === 'progress' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'progress' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">progress_activity</span>
                        ProgressBar
                    </button>
                    <button @click="activeTab = 'tooltips'"
                            :class="activeTab === 'tooltips' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'tooltips' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">tooltip_2</span>
                        Tooltips
                    </button>
                    <button @click="activeTab = 'carousel'"
                            :class="activeTab === 'carousel' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'carousel' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">view_carousel</span>
                        Carousel
                    </button>
                    <button @click="activeTab = 'calendar'"
                            :class="activeTab === 'calendar' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'calendar' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">calendar_month</span>
                        Calendar
                    </button>
                    <button @click="activeTab = 'pagination'"
                            :class="activeTab === 'pagination' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'pagination' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">last_page</span>
                        Pagination
                    </button>
                    <button @click="activeTab = 'countup'"
                            :class="activeTab === 'countup' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'countup' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">trending_up</span>
                        CountUp
                    </button>
                    <button @click="activeTab = 'scrollable'"
                            :class="activeTab === 'scrollable' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'scrollable' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">scrollable_header</span>
                        Scrollable
                    </button>
                    <button @click="activeTab = 'treeview'"
                            :class="activeTab === 'treeview' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'treeview' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">account_tree</span>
                        TreeView
                    </button>
                    <button @click="activeTab = 'maps'"
                            :class="activeTab === 'maps' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'maps' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">map</span>
                        Maps
                    </button>
                    <button @click="activeTab = 'ratings'"
                            :class="activeTab === 'ratings' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'ratings' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">star</span>
                        Ratings
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="mt-16" style="margin-top: 64px !important;">
                <!-- Tabs Tab -->
                <div x-show="activeTab === 'tabs'" x-transition>
                    <div class="text-center py-20 text-gray-400" style="padding-top: 80px !important; padding-bottom: 80px !important;">
                        <span class="material-symbols-outlined text-4xl mb-4 block">tab</span>
                        <p class="text-lg font-medium">Tabs Component Code Coming Soon</p>
                        <p class="text-sm mt-2">Tab navigation, vertical tabs, dynamic tabs akan ditambah di sini</p>
                    </div>
                </div>

                <!-- Accordions Tab -->
                <div x-show="activeTab === 'accordions'" x-transition>
                    <div class="text-center py-20 text-gray-400" style="padding-top: 80px !important; padding-bottom: 80px !important;">
                        <span class="material-symbols-outlined text-4xl mb-4 block">expand_more</span>
                        <p class="text-lg font-medium">Accordions Component Code Coming Soon</p>
                        <p class="text-sm mt-2">Collapsible panels, FAQ accordions akan ditambah di sini</p>
                    </div>
                </div>

                <!-- Notifications Tab -->
                <div x-show="activeTab === 'notifications'" x-transition>
                    <div class="text-center py-20 text-gray-400" style="padding-top: 80px !important; padding-bottom: 80px !important;">
                        <span class="material-symbols-outlined text-4xl mb-4 block">notifications</span>
                        <p class="text-lg font-medium">Notifications Component Code Coming Soon</p>
                        <p class="text-sm mt-2">Toast notifications, alert notifications akan ditambah di sini</p>
                    </div>
                </div>

                <!-- Modals Tab -->
                <div x-show="activeTab === 'modals'" x-transition>
                    <div class="text-center py-20 text-gray-400" style="padding-top: 80px !important; padding-bottom: 80px !important;">
                        <span class="material-symbols-outlined text-4xl mb-4 block">web_asset</span>
                        <p class="text-lg font-medium">Modal Component Code Coming Soon</p>
                        <p class="text-sm mt-2">Basic modals, confirmation modals, form modals akan ditambah di sini</p>
                    </div>
                </div>

                <!-- Loading Blockers Tab -->
                <div x-show="activeTab === 'loading'" x-transition>
                    <div class="text-center py-20 text-gray-400" style="padding-top: 80px !important; padding-bottom: 80px !important;">
                        <span class="material-symbols-outlined text-4xl mb-4 block">hourglass_empty</span>
                        <p class="text-lg font-medium">Loading Blockers Component Code Coming Soon</p>
                        <p class="text-sm mt-2">Full screen loaders, overlay blockers akan ditambah di sini</p>
                    </div>
                </div>

                <!-- Progress Bar Tab -->
                <div x-show="activeTab === 'progress'" x-transition>
                    <div class="text-center py-20 text-gray-400" style="padding-top: 80px !important; padding-bottom: 80px !important;">
                        <span class="material-symbols-outlined text-4xl mb-4 block">progress_activity</span>
                        <p class="text-lg font-medium">Progress Bar Component Code Coming Soon</p>
                        <p class="text-sm mt-2">Linear progress, circular progress akan ditambah di sini</p>
                    </div>
                </div>

                <!-- Tooltips & Popovers Tab -->
                <div x-show="activeTab === 'tooltips'" x-transition>
                    <div class="text-center py-20 text-gray-400" style="padding-top: 80px !important; padding-bottom: 80px !important;">
                        <span class="material-symbols-outlined text-4xl mb-4 block">help</span>
                        <p class="text-lg font-medium">Tooltips & Popovers Component Code Coming Soon</p>
                        <p class="text-sm mt-2">Hover tooltips, click popovers akan ditambah di sini</p>
                    </div>
                </div>

                <!-- Carousel Tab -->
                <div x-show="activeTab === 'carousel'" x-transition>
                    <div class="text-center py-20 text-gray-400" style="padding-top: 80px !important; padding-bottom: 80px !important;">
                        <span class="material-symbols-outlined text-4xl mb-4 block">view_carousel</span>
                        <p class="text-lg font-medium">Carousel Component Code Coming Soon</p>
                        <p class="text-sm mt-2">Image carousel, content slider akan ditambah di sini</p>
                    </div>
                </div>

                <!-- Calendar Tab -->
                <div x-show="activeTab === 'calendar'" x-transition>
                    <div class="text-center py-20 text-gray-400" style="padding-top: 80px !important; padding-bottom: 80px !important;">
                        <span class="material-symbols-outlined text-4xl mb-4 block">calendar_month</span>
                        <p class="text-lg font-medium">Calendar Component Code Coming Soon</p>
                        <p class="text-sm mt-2">Date picker, event calendar akan ditambah di sini</p>
                    </div>
                </div>

                <!-- Pagination Tab -->
                <div x-show="activeTab === 'pagination'" x-transition>
                    <div class="text-center py-20 text-gray-400" style="padding-top: 80px !important; padding-bottom: 80px !important;">
                        <span class="material-symbols-outlined text-4xl mb-4 block">more_horiz</span>
                        <p class="text-lg font-medium">Pagination Component Code Coming Soon</p>
                        <p class="text-sm mt-2">Page navigation, infinite scroll akan ditambah di sini</p>
                    </div>
                </div>

                <!-- Count Up Tab -->
                <div x-show="activeTab === 'countup'" x-transition>
                    <div class="text-center py-20 text-gray-400" style="padding-top: 80px !important; padding-bottom: 80px !important;">
                        <span class="material-symbols-outlined text-4xl mb-4 block">trending_up</span>
                        <p class="text-lg font-medium">Count Up Component Code Coming Soon</p>
                        <p class="text-sm mt-2">Animated counters, statistics akan ditambah di sini</p>
                    </div>
                </div>

                <!-- Scrollable Tab -->
                <div x-show="activeTab === 'scrollable'" x-transition>
                    <div class="text-center py-20 text-gray-400" style="padding-top: 80px !important; padding-bottom: 80px !important;">
                        <span class="material-symbols-outlined text-4xl mb-4 block">scrollable_header</span>
                        <p class="text-lg font-medium">Scrollable Component Code Coming Soon</p>
                        <p class="text-sm mt-2">Scrollable containers, virtual scroll akan ditambah di sini</p>
                    </div>
                </div>

                <!-- Tree View Tab -->
                <div x-show="activeTab === 'treeview'" x-transition>
                    <div class="text-center py-20 text-gray-400" style="padding-top: 80px !important; padding-bottom: 80px !important;">
                        <span class="material-symbols-outlined text-4xl mb-4 block">account_tree</span>
                        <p class="text-lg font-medium">Tree View Component Code Coming Soon</p>
                        <p class="text-sm mt-2">Hierarchical data, expandable tree akan ditambah di sini</p>
                    </div>
                </div>

                <!-- Maps Tab -->
                <div x-show="activeTab === 'maps'" x-transition>
                    <div class="text-center py-20 text-gray-400" style="padding-top: 80px !important; padding-bottom: 80px !important;">
                        <span class="material-symbols-outlined text-4xl mb-4 block">map</span>
                        <p class="text-lg font-medium">Maps Component Code Coming Soon</p>
                        <p class="text-sm mt-2">Interactive maps, location picker akan ditambah di sini</p>
                    </div>
                </div>

                <!-- Ratings Tab -->
                <div x-show="activeTab === 'ratings'" x-transition>
                    <div class="text-center py-20 text-gray-400" style="padding-top: 80px !important; padding-bottom: 80px !important;">
                        <span class="material-symbols-outlined text-4xl mb-4 block">star</span>
                        <p class="text-lg font-medium">Ratings Component Code Coming Soon</p>
                        <p class="text-sm mt-2">Star ratings, review system akan ditambah di sini</p>
                    </div>
                </div>
            </div>
        </div>
    </x-ui.page-header>
</x-dashboard-layout>

