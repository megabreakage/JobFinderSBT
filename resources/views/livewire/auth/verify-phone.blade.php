<div class="max-w-md mx-auto">
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="text-center mb-6">
            @if($verified)
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Phone Verified!</h2>
            @else
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Verify Your Phone</h2>
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

        @if($verified)
            <div class="text-center">
                <p class="text-gray-600 mb-6">
                    Your phone number has been successfully verified. You can now receive SMS notifications.
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
            @if(!$otpSent)
                <div class="text-center">
                    <p class="text-gray-600 mb-6">
                        Click the button below to receive a 6-digit verification code via SMS to your registered phone number.
                    </p>
                    
                    @auth
                        <p class="text-sm text-gray-500 mb-6">
                            Phone: {{ Auth::user()->phone }}
                        </p>
                    @endauth

                    <x-ui.button
                        wire:click="sendOtp"
                        variant="primary"
                        size="lg"
                        class="w-full"
                        :disabled="$resendCooldown > 0"
                    >
                        @if($resendCooldown > 0)
                            Wait {{ $resendCooldown }}s
                        @else
                            Send OTP
                        @endif
                    </x-ui.button>
                </div>
            @else
                <form wire:submit.prevent="verifyOtp">
                    <div class="mb-6">
                        <p class="text-gray-600 text-center mb-4">
                            Enter the 6-digit code sent to your phone number.
                        </p>

                        <x-form.input
                            label="OTP Code"
                            name="otp"
                            type="text"
                            wire:model="otp"
                            placeholder="123456"
                            required
                            maxlength="6"
                            :error="$errors->first('otp')"
                            class="text-center text-2xl tracking-widest"
                        />
                    </div>

                    <x-ui.button
                        type="submit"
                        variant="primary"
                        size="lg"
                        class="w-full mb-4"
                    >
                        Verify OTP
                    </x-ui.button>

                    <div class="text-center">
                        <p class="text-sm text-gray-500 mb-2">
                            Didn't receive the code?
                        </p>
                        <button
                            type="button"
                            wire:click="sendOtp"
                            class="text-sm font-medium text-indigo-600 hover:text-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            @if($resendCooldown > 0) disabled @endif
                        >
                            @if($resendCooldown > 0)
                                Resend in {{ $resendCooldown }}s
                            @else
                                Resend OTP
                            @endif
                        </button>
                    </div>
                </form>
            @endif
        @endif
    </div>
</div>
