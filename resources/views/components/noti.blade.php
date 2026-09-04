@if (session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            width: 400,
            height: 350,
            text: "{{ session('success') }}",
            confirmButtonText: 'OK'
        });
    });
</script>
@endif

@if (session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            width: 400,
            height: 350,
            text: "{{ session('error') }}",
            confirmButtonText: 'OK'
        });
    });
</script>
@endif

@if (session('warning'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'warning',
            title: 'Warning',
            width: 400,
            height: 350,
            text: "{{ session('warning') }}",
            confirmButtonText: 'OK'
        });
    });
</script>
@endif

@if (session('info'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'info',
            title: 'Information',
            width: 400,
            height: 350,
            text: "{{ session('info') }}",
            confirmButtonText: 'OK'
        });
    });
</script>
@endif

@if ($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            width: 400,
            height: 350,
            html: `
                    <div class="text-start">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                `,
            confirmButtonText: 'OK'
        });
    });
</script>
@endif