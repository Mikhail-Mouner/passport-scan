<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Posts CRUD')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('posts.index') }}">Posts CRUD</a>
        </div>
    </nav>

    <div class="container mt-4">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JSON Modal -->
    <div class="modal fade" id="jsonModal" tabindex="-1" aria-labelledby="jsonModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="jsonModalLabel">JSON Response</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <pre id="jsonContent" style="white-space: pre-wrap; word-wrap: break-word;"></pre>
                </div>
            </div>
        </div>
    </div>

    <script>
        function processImage(id) {
            const url = "{{ route('posts.process') }}";
            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        id: id
                    })
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('jsonContent').textContent = JSON.stringify(data, null, 2);
                    const modal = new bootstrap.Modal(document.getElementById('jsonModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    const modal = new bootstrap.Modal(document.getElementById('jsonModal'));
                    const errorMessage = document.createElement('div');
                    errorMessage.className = 'alert alert-danger';
                    errorMessage.textContent = error.message;
                    document.getElementById('jsonContent').parentNode.insertBefore(errorMessage, document.getElementById('jsonContent'));
                    modal.show();
                    // alert('Error processing image');
                });
        }
    </script>

</body>

</html>
