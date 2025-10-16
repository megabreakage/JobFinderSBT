<x-layouts.guest>
    <livewire:auth.reset-password :token="$token" :email="request('email')" />
</x-layouts.guest>
