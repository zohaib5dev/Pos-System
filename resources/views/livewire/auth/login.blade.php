<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $user = Auth::user();
        if ($user->hasRole('Cashier')) {
            $this->redirectRoute('pos.index', navigate: false);
            return;
        } else {
            $this->redirectIntended(
                default: route('dashboard', absolute: false),
                navigate: false
            );
        }
    }
};

?>

<div>
    <div class="flex items-center justify-center bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900 py-12 px-4 sm:px-6 lg:px-4">
        <div class="absolute inset-0 bg-grid-pattern opacity-10"></div>
        <div class="max-w-md w-full space-y-8 relative z-10">
            <div class="text-center mb-6">
                <img src="{{ getLogo() }}" alt="POS Logo" class="mx-auto w-48 h-auto">
                <h3 class="text-blue-200 font-bold mt-2">
                    Welcome back!
                </h3>
                <p class="text-sm text-blue-100/80">
                    Sign in to continue
                </p>
            </div>
            <x-auth-session-status class="mb-4" :status="session('status')" />
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl p-8 border border-white/20">
                <form wire:submit="login" class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-blue-100 mb-2">
                            <i class="fas fa-envelope mr-2"></i>Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-blue-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                            </div>
                            <x-text-input
                                wire:model="form.email"
                                id="email"
                                class="block w-full pl-10 pr-3 py-3 border-0 bg-white/20 backdrop-blur-sm text-white placeholder-blue-200 rounded-xl focus:ring-2 focus:ring-blue-400 focus:bg-white/30 transition-all duration-200"
                                type="email"
                                name="email"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="email@pos.com" />
                        </div>
                        <x-input-error :messages="$errors->get('form.email')" class="mt-2 text-pink-200 text-sm" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-blue-100 mb-2">
                            <i class="fas fa-lock mr-2"></i>Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-blue-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <x-text-input
                                wire:model="form.password"
                                id="password"
                                class="block w-full pl-10 pr-10 py-3 border-0 bg-white/20 backdrop-blur-sm text-white placeholder-blue-200 rounded-xl focus:ring-2 focus:ring-blue-400 focus:bg-white/30 transition-all duration-200"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••" />

                            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg id="eye-icon" class="h-5 w-5 text-blue-300 hover:text-blue-100 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('form.password')" class="mt-2 text-pink-200 text-sm" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label for="remember" class="flex items-center">
                            <input wire:model="form.remember" id="remember" type="checkbox" class="h-4 w-4 bg-white/20 border-0 rounded text-blue-600 focus:ring-blue-500 focus:ring-offset-0 transition-colors">
                            <span class="ml-2 block text-sm text-blue-100">
                                {{ __('Remember me') }}
                            </span>
                        </label>

                        @if (Route::has('password.request'))
                        <a class="text-sm text-blue-200 hover:text-white transition-colors" href="{{ route('password.request') }}" wire:navigate>
                            {{ __('Forgot password?') }}
                        </a>
                        @endif
                    </div>

                    <div>
                        <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-xl text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transform transition-all duration-200 hover:scale-105 shadow-lg hover:shadow-xl">
                            <span class="  inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-blue-300 group-hover:text-blue-200 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                </svg>
                            </span>
                            {{ __('Sign In') }}
                        </button>
                    </div>

                </form>
            </div>


        </div>
    </div>


    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
            }
        }
    </script>

</div>