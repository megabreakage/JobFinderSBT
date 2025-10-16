<div class="max-w-md mx-auto">
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="text-center mb-6">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 mb-4">
                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Forgot Password?</h2>
            <p class="mt-2 text-sm text-gray-600">
                No worries! Enter your email address and we'll send you a link to reset your password.
            </p>
        </div>

        @if($success)
            <x-ui.alert type="success" class="mb-4">
                {{ $message }}
            </x-ui.alert>
        @endif

        @if($errorMessage)
            <x-ui.alert type="error" class="mb-4">
                {{ $errorMessage }}
            </x-ui.alert>
        @endif

        @if(!$success)
            <form wire:submit.prevent="sendResetLink">
                <x-form.input
                    label="Email Address"
                    name="email"
                    type="email"
                    wire:model="email"
                    placeholder="john.doe@example.com"
                    required
                    :error="$errors->first('email')"
                />

                <x-ui.button
                    type="submit"
                    variant="primary"
                    size="lg"
                    class="w-full"
                >
                    Send Reset Link
                </x-ui.button>
            </form>
        @else
            <div class="text-center">
                <x-ui.button
                    href="{{ route('login') }}"
                    variant="primary"
                    size="lg"
                    class="w-full"
                >
                    Back to Login
                </x-ui.button>
            </div>
        @endif

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                ← Back to login
            </a>
        </div>
    </div>
</div>
