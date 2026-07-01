<x-guest-layout>
    {{-- Message d'explication en français --}}
    <div class="mb-5 text-sm text-gray-600 leading-relaxed">
        {{ __('Forgot password instruction') }}
    </div>

    {{-- Message de succès (après envoi du mail) --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        {{-- Adresse e-mail --}}
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="w-full justify-center">
                {{ __('Send Reset Link') }}
            </x-primary-button>
        </div>
    </form>

    {{-- Lien retour vers la connexion --}}
    <div class="mt-5 text-center border-t border-slate-100 pt-4">
        <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-[#00bac6] transition-colors flex items-center justify-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            {{ __('Back to login') }}
        </a>
    </div>
</x-guest-layout>
