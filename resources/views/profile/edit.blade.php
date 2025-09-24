<x-dashboard-layout title="Profile">


    <!-- Profile Container -->
    <x-ui.page-header
        title="Profil"
        description="Kemaskini maklumat profil dan kata laluan anda"
    >
        <div class="space-y-6">
            <x-ui.container class="w-full">
                @include('profile.partials.update-profile-information-form')
            </x-ui.container>

            <x-ui.container class="w-full">
                @include('profile.partials.update-password-form')
            </x-ui.container>

            <x-ui.container class="w-full">
                @include('profile.partials.delete-user-form')
            </x-ui.container>
        </div>
    </x-ui.page-header>
</x-dashboard-layout>
