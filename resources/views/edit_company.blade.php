<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Account') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm p-8">
                <form method="POST" action="{{ route('updateCompany', $company->id) }}">
                    @method('PATCH')
                    @csrf
                    <div class="flex flex-wrap gap-y-4">
                        <div class="sm:w-1/2 sm:pr-2">
                            <label for="name">Name</label>
                            <input id="name" class="block mt-1 w-full" type="text" name="name" value="{{ $company->name }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pl-2">
                            <label for="name">Legal Name</label>
                            <input id="name" class="block mt-1 w-full" type="text" name="legal_name" value="{{ $company->legal_name }}" required autofocus />
                        </div>
                        <div class="sm:w-1/2 sm:pr-2">
                            <label for="domain">Domain</label>
                            <input id="domain" class="block mt-1 w-full" type="text" name="domain" value="{{ $company->domain }}" required />
                        </div>
                        <div class="sm:w-1/2 sm:pl-2">
                            <label for="country">Country</label>
                            <input id="country" class="block mt-1 w-full" type="text" name="country" value="{{ $company->country }}" required />
                        </div>
                        <div class="sm:w-1/2 sm:pr-2">
                            <label for="revenue">Revenue in Mio. Euro</label>
                            <input id="revenue" class="block mt-1 w-full" type="text" name="revenue" value="{{ $company->revenue }}" />
                        </div>
                        <div class="sm:w-1/2 sm:pl-2">
                            <label for="wz_code">WZ Code</label>
                            <input id="wz_code" class="block mt-1 w-full" type="text" name="wz_code" value="{{ $company->wz_code }}" />
                        </div>
                        <div class="sm:w-1/2 sm:pr-2">
                            <label for="headcount">Headcount</label>
                            <input id="headcount" class="block mt-1 w-full" type="text" name="headcount" value="{{ $company->headcount }}" />
                        </div>
                    </div>
                    <div class="flex items-center justify-end mt-4">
                        <button class="ml-4  btn-bg-primary text-white  py-2 px-4">
                            {{ __('Update') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <style>
     @media(max-width:767px){
        form .flex div {
          width: 100%;
          padding:0px;
        }
        .ml-4.btn-bg-primary {
            width: 100%;
            margin: 0px;
        }
     }
    </style>
</x-app-layout>
