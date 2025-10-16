{{-- PHPFlasher Toastr Notifications Component --}}
{{-- This component renders notifications using PHPFlasher with Livewire persistence --}}

{!! flasher_render() !!}

{{-- Legacy session flash support --}}
@if(session()->has('success'))
    @php flash()->success(session('success')); @endphp
@endif

@if(session()->has('error'))
    @php flash()->error(session('error')); @endphp
@endif

@if(session()->has('warning'))
    @php flash()->warning(session('warning')); @endphp
@endif

@if(session()->has('info'))
    @php flash()->info(session('info')); @endphp
@endif

{{-- Validation errors --}}
@if($errors->any())
    @foreach($errors->all() as $error)
        @php flash()->error($error, 'Validation Error'); @endphp
    @endforeach
@endif
