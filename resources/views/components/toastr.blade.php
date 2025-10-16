{{-- Toastr Notifications Component --}}
{{-- This component handles session flash messages and displays them as toastr notifications --}}

@if(session()->has('toast'))
    @php
        $toast = session('toast');
        $type = $toast['type'] ?? 'info';
        $message = $toast['message'] ?? '';
        $title = $toast['title'] ?? '';
    @endphp
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            toastr.{{ $type }}('{{ $message }}', '{{ $title }}');
        });
    </script>
@endif

@if(session()->has('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            toastr.success('{{ session('success') }}', 'Success');
        });
    </script>
@endif

@if(session()->has('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            toastr.error('{{ session('error') }}', 'Error');
        });
    </script>
@endif

@if(session()->has('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            toastr.warning('{{ session('warning') }}', 'Warning');
        });
    </script>
@endif

@if(session()->has('info'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            toastr.info('{{ session('info') }}', 'Info');
        });
    </script>
@endif

@if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($errors->all() as $error)
                toastr.error('{{ $error }}', 'Validation Error');
            @endforeach
        });
    </script>
@endif
