<div class="max-w-md mx-auto">
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="text-center mb-6">
            @if($verified)
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Email Verified!</h2>
            @elseif($invalid)
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Verification Failed</h2>
            @elseif($alreadyVerified)
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Already Verified</h2>
            @else
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Verify Your Email</h2>
            @endif
        </div>

        @if($message)
            <x-ui.alert 
                :type="$verified || $resendSuccess ? 'success' : ($invalid || $resendError ? 'error' : 'info')" 
                class="mb-4"
            >
                {{ $message }}
            </x-ui.alert>
        @endif

        @if($verified)
            <div class="text-center">
                <p class="text-gray-600 mb-6">
                    Your email has been successfully verified. You can now access all features of your account.
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
                    The verification link is invalid or has expired. Please request a new verification email.
                </p>
                @auth
                    <x-ui.button
                        wire:click="resendVerification"
                        variant="primary"
                        size="lg"
                        class="w-full"
                    >
                        Resend Verification Email
                    </x-ui.button>
                @else
                    <x-ui.button
                        href="{{ route('login') }}"
                        variant="primary"
                        size="lg"
                        class="w-full"
                    >
                        Go to Login
                    </x-ui.button>
                @endauth
            </div>
        @elseif($alreadyVerified)
            <div class="text-center">
                <p class="text-gray-600 mb-6">
                    Your email address has already been verified.
                </p>
                <x-ui.button
                    href="/dashboard"
                    variant="primary"
                    size="lg"
                    class="w-full"
                >
                    Go to Dashboard
                </x-ui.button>
            </div>
        @else
            <div class="text-center">
                <p class="text-gray-600 mb-6">
                    We've sent a verification email to your registered email address. Please check your inbox and click the verification link.
                </p>
                
                <p class="text-sm text-gray-500 mb-6">
                    Didn't receive the email? Check your spam folder or request a new one.
                </p>

                @auth
                    <x-ui.button
                        wire:click="resendVerification"
                        variant="outline"
                        size="lg"
                        class="w-full"
                    >
                        Resend Verification Email
                    </x-ui.button>
                @endauth
            </div>
        @endif
    </div>
</div>
