<div class="max-w-md mx-auto">
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="text-center mb-6">
            @if($success)
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Password Reset!</h2>
            @elseif($invalid)
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Invalid Link</h2>
            @else
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 mb-4">
                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Reset Password</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Enter your new password below.
                </p>
            @endif
        </div>

        @if($message)
            <x-ui.alert type="success" class="mb-4">
                {{ $message }}
            </x-ui.alert>
        @endif

        @if($errorMessage)
            <x-ui.alert type="error" class="mb-4">
                {{ $errorMessage }}
            </x-ui.alert>
        @endif

        @if($success)
            <div class="text-center">
                <p class="text-gray-600 mb-6">
                    Your password has been successfully reset. You can now log in with your new password.
                </p>
                <x-ui.button
                    href="{{ route('login') }}"
                    variant="primary"
                    size="lg"
                    class="w-full"
                >
                    Go to Login
                </x-ui.button>
            </div>
        @elseif($invalid)
            <div class="text-center">
                <p class="text-gray-600 mb-6">
                    This password reset link is invalid or has expired. Please request a new password reset link.
                </p>
                <x-ui.button
                    href="{{ route('password.request') }}"
                    variant="primary"
                    size="lg"
                    class="w-full"
                >
                    Request New Link
                </x-ui.button>
            </div>
        @else
            <form wire:submit.prevent="resetPassword">
                <x-form.input
                    label="Email Address"
                    name="email"
                    type="email"
                    wire:model="email"
                    placeholder="john.doe@example.com"
                    required
                    readonly
                    :error="$errors->first('email')"
                />

                <x-form.input
                    label="New Password"
                    name="password"
                    type="password"
                    wire:model="password"
                    placeholder="••••••••"
                    required
                    hint="Minimum 8 characters"
                    :error="$errors->first('password')"
                />

                <x-form.input
                    label="Confirm New Password"
                    name="password_confirmation"
                    type="password"
                    wire:model="password_confirmation"
                    placeholder="••••••••"
                    required
                    :error="$errors->first('password_confirmation')"
                />

                <x-ui.button
                    type="submit"
                    variant="primary"
                    size="lg"
                    class="w-full"
                >
                    Reset Password
                </x-ui.button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    ← Back to login
                </a>
            </div>
        @endif
    </div>
</div>
