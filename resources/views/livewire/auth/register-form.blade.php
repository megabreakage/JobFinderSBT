<div class="max-w-md mx-auto">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Create Account</h2>

        @if($success)
            <x-ui.alert type="success" class="mb-4">
                {{ $successMessage }}
            </x-ui.alert>
        @endif

        @if($errorMessage)
            <x-ui.alert type="error" class="mb-4">
                {{ $errorMessage }}
            </x-ui.alert>
        @endif

        <form wire:submit.prevent="submit">
            <div class="grid grid-cols-2 gap-4">
                <x-form.input
                    label="First Name"
                    name="first_name"
                    wire:model="first_name"
                    placeholder="John"
                    required
                    :error="$errors->first('first_name')"
                />

                <x-form.input
                    label="Last Name"
                    name="last_name"
                    wire:model="last_name"
                    placeholder="Doe"
                    required
                    :error="$errors->first('last_name')"
                />
            </div>

            <x-form.input
                label="Email Address"
                name="email"
                type="email"
                wire:model="email"
                placeholder="john.doe@example.com"
                required
                :error="$errors->first('email')"
            />

            <x-form.input
                label="Phone Number"
                name="phone"
                type="tel"
                wire:model="phone"
                placeholder="+1234567890"
                required
                :error="$errors->first('phone')"
            />

            <x-form.select
                label="I am a"
                name="role"
                wire:model="role"
                :options="[
                    'job-seeker' => 'Job Seeker',
                    'employer' => 'Employer'
                ]"
                required
                :error="$errors->first('role')"
            />

            <x-form.input
                label="Password"
                name="password"
                type="password"
                wire:model="password"
                placeholder="••••••••"
                required
                hint="Minimum 8 characters"
                :error="$errors->first('password')"
            />

            <x-form.input
                label="Confirm Password"
                name="password_confirmation"
                type="password"
                wire:model="password_confirmation"
                placeholder="••••••••"
                required
                :error="$errors->first('password_confirmation')"
            />

            <div class="mb-4">
                <label class="flex items-start">
                    <input
                        type="checkbox"
                        wire:model="terms_accepted"
                        class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                    <span class="ml-2 text-sm text-gray-600">
                        I agree to the <a href="#" class="text-indigo-600 hover:text-indigo-500">Terms and Conditions</a> and <a href="#" class="text-indigo-600 hover:text-indigo-500">Privacy Policy</a>
                    </span>
                </label>
                @error('terms_accepted')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <x-ui.button
                type="submit"
                variant="primary"
                size="lg"
                class="w-full"
            >
                Create Account
            </x-ui.button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Already have an account?
                <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Sign in
                </a>
            </p>
        </div>
    </div>
</div>
