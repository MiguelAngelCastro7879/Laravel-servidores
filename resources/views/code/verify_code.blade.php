<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('last_code') }}">
        @csrf
        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <span class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Verify Code') }}</span>
            </label>
            <br><br>
        </div>

        <!-- Code Input -->
            <x-input-label for="login_code" :value="__('Code')" />
        <div >
            <x-text-input id="login_code" class="block mt-1 w-full" type="text" name="login_code" required autofocus/>
            <x-input-error :messages="$errors->get('login_code')" class="mt-2" />
                <br>
        </div>


        <div class="flex items-center justify-end mt-4">

            <x-primary-button class="ml-3">
                {{ __('Verify') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
