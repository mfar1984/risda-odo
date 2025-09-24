<x-dashboard-layout title="Element">
    <x-ui.page-header
        title="Element"
        description="Basic UI elements untuk pembangunan aplikasi"
    >
        <!-- Tab Navigation -->
        <div class="mb-8" x-data="{ activeTab: 'buttons' }">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Tabs">
                    <button @click="activeTab = 'buttons'"
                            :class="activeTab === 'buttons' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'buttons' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">radio_button_checked</span>
                        Buttons
                    </button>
                    <button @click="activeTab = 'dropdown'"
                            :class="activeTab === 'dropdown' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'dropdown' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">expansion_panels</span>
                        Dropdown
                    </button>
                    <button @click="activeTab = 'icons'"
                            :class="activeTab === 'icons' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'icons' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">emoji_symbols</span>
                        Icons
                    </button>
                    <button @click="activeTab = 'badges'"
                            :class="activeTab === 'badges' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'badges' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">label</span>
                        Badges
                    </button>
                    <button @click="activeTab = 'cards'"
                            :class="activeTab === 'cards' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'cards' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">credit_card</span>
                        Cards
                    </button>
                    <button @click="activeTab = 'loading'"
                            :class="activeTab === 'loading' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'loading' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">sync</span>
                        Loading
                    </button>
                    <button @click="activeTab = 'lists'"
                            :class="activeTab === 'lists' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'lists' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">communities</span>
                        ListGroups
                    </button>
                    <button @click="activeTab = 'navigation'"
                            :class="activeTab === 'navigation' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'navigation' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">dvr</span>
                        Navigation
                    </button>
                    <button @click="activeTab = 'timeline'"
                            :class="activeTab === 'timeline' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'timeline' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">timeline</span>
                        Timeline
                    </button>
                    <button @click="activeTab = 'utilities'"
                            :class="activeTab === 'utilities' ? 'text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="whitespace-nowrap py-3 px-2 font-medium transition-colors duration-200 flex items-center gap-2"
                            :style="activeTab === 'utilities' ? 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid #2563eb !important; color: #2563eb !important;' : 'font-family: Poppins, sans-serif !important; font-size: 12px !important; font-weight: 500 !important; border-bottom: 3px solid transparent !important;'">
                        <span class="material-symbols-outlined" style="font-size: 16px;">build</span>
                        Utilities
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="mt-16" style="margin-top: 64px !important;">
                <!-- Buttons Tab -->
                <div x-show="activeTab === 'buttons'" x-transition>
                    <div class="space-y-8">
                        <!-- Button Standard/Default -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Button Standard/Default</h3>
                            <div class="bg-gray-50 rounded p-4 text-center text-gray-500">
                                <p class="text-sm">Component akan ditambah di sini</p>
                            </div>
                        </div>

                        <!-- Pill Shape Button -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Pill Shape Button (Rounded/Oval)</h3>
                            <div class="bg-gray-50 rounded p-4 text-center text-gray-500">
                                <p class="text-sm">Component akan ditambah di sini</p>
                            </div>
                        </div>

                        <!-- Square/Rectangular Button -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Square/Rectangular Button</h3>
                            <div class="bg-gray-50 rounded p-4 text-center text-gray-500">
                                <p class="text-sm">Component akan ditambah di sini</p>
                            </div>
                        </div>

                        <!-- Outline Button -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Outline Button (Ghost Button)</h3>
                            <div class="bg-gray-50 rounded p-4 text-center text-gray-500">
                                <p class="text-sm">Component akan ditambah di sini</p>
                            </div>
                        </div>

                        <!-- Block Level Button -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Block Level Button</h3>
                            <div class="bg-gray-50 rounded p-4 text-center text-gray-500">
                                <p class="text-sm">Component akan ditambah di sini</p>
                            </div>
                        </div>

                        <!-- Icon Button -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Icon Button</h3>
                            <div class="bg-gray-50 rounded p-4 text-center text-gray-500">
                                <p class="text-sm">Component akan ditambah di sini</p>
                            </div>
                        </div>

                        <!-- Link Button -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Link Button (Anchor Button)</h3>
                            <div class="bg-gray-50 rounded p-4 text-center text-gray-500">
                                <p class="text-sm">Component akan ditambah di sini</p>
                            </div>
                        </div>

                        <!-- Button Groups -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Button Groups</h3>
                            <div class="bg-gray-50 rounded p-4 text-center text-gray-500">
                                <p class="text-sm">Component akan ditambah di sini</p>
                            </div>
                        </div>

                        <!-- Split Buttons -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Split Buttons (Dropdown with Button)</h3>
                            <div class="bg-gray-50 rounded p-4 text-center text-gray-500">
                                <p class="text-sm">Component akan ditambah di sini</p>
                            </div>
                        </div>

                        <!-- Floating Action Button -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Floating Action Button (FAB)</h3>
                            <div class="bg-gray-50 rounded p-4 text-center text-gray-500">
                                <p class="text-sm">Component akan ditambah di sini</p>
                            </div>
                        </div>

                        <!-- Toggle Button -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Toggle Button</h3>
                            <div class="bg-gray-50 rounded p-4 text-center text-gray-500">
                                <p class="text-sm">Component akan ditambah di sini</p>
                            </div>
                        </div>

                        <!-- Loading Button -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Loading Button</h3>
                            <div class="bg-gray-50 rounded p-4 text-center text-gray-500">
                                <p class="text-sm">Component akan ditambah di sini</p>
                            </div>
                        </div>

                        <!-- Social Media Button -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Social Media Button</h3>
                            <div class="bg-gray-50 rounded p-4 text-center text-gray-500">
                                <p class="text-sm">Component akan ditambah di sini</p>
                            </div>
                        </div>

                        <!-- Gradient Button -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Gradient Button</h3>
                            <div class="bg-gray-50 rounded p-4 text-center text-gray-500">
                                <p class="text-sm">Component akan ditambah di sini</p>
                            </div>
                        </div>

                        <!-- Neumorphism Button -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Neumorphism Button</h3>
                            <div class="bg-gray-50 rounded p-4 text-center text-gray-500">
                                <p class="text-sm">Component akan ditambah di sini</p>
                            </div>
                        </div>

                        <!-- 3D Button -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">3D Button</h3>
                            <div class="bg-gray-50 rounded p-4 text-center text-gray-500">
                                <p class="text-sm">Component akan ditambah di sini</p>
                            </div>
                        </div>

                        <!-- Animated Button -->
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4" style="font-family: Poppins, sans-serif !important; font-size: 14px !important;">Animated Button</h3>
                            <div class="bg-gray-50 rounded p-4 text-center text-gray-500">
                                <p class="text-sm">Component akan ditambah di sini</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dropdown Tab -->
                <div x-show="activeTab === 'dropdown'" x-transition>
                    <div class="text-center py-20 text-gray-400" style="padding-top: 80px !important; padding-bottom: 80px !important;">
                        <span class="material-symbols-outlined text-4xl mb-4 block">arrow_drop_down</span>
                        <p class="text-lg font-medium">Dropdown Element Code Coming Soon</p>
                        <p class="text-sm mt-2">Basic dropdown elements akan ditambah di sini</p>
                    </div>
                </div>

                <!-- Icons Tab -->
                <div x-show="activeTab === 'icons'" x-transition>
                    <div class="text-center py-20 text-gray-400" style="padding-top: 80px !important; padding-bottom: 80px !important;">
                        <span class="material-symbols-outlined text-4xl mb-4 block">emoji_symbols</span>
                        <p class="text-lg font-medium">Icons Element Code Coming Soon</p>
                        <p class="text-sm mt-2">Material Symbols, SVG icons akan ditambah di sini</p>
                    </div>
                </div>

                <!-- Badges Tab -->
                <div x-show="activeTab === 'badges'" x-transition>
                    <div class="text-center py-20 text-gray-400" style="padding-top: 80px !important; padding-bottom: 80px !important;">
                        <span class="material-symbols-outlined text-4xl mb-4 block">label</span>
                        <p class="text-lg font-medium">Badges Element Code Coming Soon</p>
                        <p class="text-sm mt-2">Status badges, notification badges akan ditambah di sini</p>
                    </div>
                </div>

                <!-- Cards Tab -->
                <div x-show="activeTab === 'cards'" x-transition>
                    <div class="text-center py-20 text-gray-400" style="padding-top: 80px !important; padding-bottom: 80px !important;">
                        <span class="material-symbols-outlined text-4xl mb-4 block">credit_card</span>
                        <p class="text-lg font-medium">Cards Element Code Coming Soon</p>
                        <p class="text-sm mt-2">Content cards, info cards akan ditambah di sini</p>
                    </div>
                </div>

                <!-- Loading Indicators Tab -->
                <div x-show="activeTab === 'loading'" x-transition>
                    <div class="text-center py-20 text-gray-400" style="padding-top: 80px !important; padding-bottom: 80px !important;">
                        <span class="material-symbols-outlined text-4xl mb-4 block">sync</span>
                        <p class="text-lg font-medium">Loading Indicators Element Code Coming Soon</p>
                        <p class="text-sm mt-2">Spinners, skeleton loaders akan ditambah di sini</p>
                    </div>
                </div>

                <!-- List Groups Tab -->
                <div x-show="activeTab === 'lists'" x-transition>
                    <div class="text-center py-20 text-gray-400" style="padding-top: 80px !important; padding-bottom: 80px !important;">
                        <span class="material-symbols-outlined text-4xl mb-4 block">list</span>
                        <p class="text-lg font-medium">List Groups Element Code Coming Soon</p>
                        <p class="text-sm mt-2">Ordered lists, unordered lists akan ditambah di sini</p>
                    </div>
                </div>

                <!-- Navigation Menus Tab -->
                <div x-show="activeTab === 'navigation'" x-transition>
                    <div class="text-center py-20 text-gray-400" style="padding-top: 80px !important; padding-bottom: 80px !important;">
                        <span class="material-symbols-outlined text-4xl mb-4 block">menu</span>
                        <p class="text-lg font-medium">Navigation Menus Element Code Coming Soon</p>
                        <p class="text-sm mt-2">Menu bars, breadcrumbs akan ditambah di sini</p>
                    </div>
                </div>

                <!-- Timeline Tab -->
                <div x-show="activeTab === 'timeline'" x-transition>
                    <div class="text-center py-20 text-gray-400" style="padding-top: 80px !important; padding-bottom: 80px !important;">
                        <span class="material-symbols-outlined text-4xl mb-4 block">timeline</span>
                        <p class="text-lg font-medium">Timeline Element Code Coming Soon</p>
                        <p class="text-sm mt-2">Activity timeline, progress timeline akan ditambah di sini</p>
                    </div>
                </div>

                <!-- Utilities Tab -->
                <div x-show="activeTab === 'utilities'" x-transition>
                    <div class="text-center py-20 text-gray-400" style="padding-top: 80px !important; padding-bottom: 80px !important;">
                        <span class="material-symbols-outlined text-4xl mb-4 block">build</span>
                        <p class="text-lg font-medium">Utilities Element Code Coming Soon</p>
                        <p class="text-sm mt-2">Helper classes, spacing utilities akan ditambah di sini</p>
                    </div>
                </div>
            </div>
        </div>
    </x-ui.page-header>
</x-dashboard-layout>
