<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12 px-6 ">
        <div class="profile-page max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 flex gap-4 flex-wrap">
            <div class="card-profile p-4 sm:p-8 bg-white shadow sm:rounded-lg flex-1">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="card-profile p-4 sm:p-8 bg-white shadow sm:rounded-lg flex-1">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="card-profile full-card p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>

    <style>
    .card-profile {
    height: inherit !important;
    margin: 0px !important;
    }
    .card-profile button.inline-flex {
    border-radius: 0px;
    }
    input {
        border-radius:0px !important;
    }
    .card-profile.full-card {
        width: 100%;
    }
    @media(max-width:641px){
    .card-profile {
        width: 100%;
        flex: unset;
    }
    }    
    </style>    
</x-app-layout>


