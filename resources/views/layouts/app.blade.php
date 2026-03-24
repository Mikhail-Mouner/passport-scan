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
    <script type="text/javascript" src="https://cdn.asprise.com/scannerjs/scanner.js"></script>
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
        let baseUrl = "{{url("/")}}";
        function processImage(e, id) {
            e.target.innerHTML = 'Processing...';
            e.target.disabled = true;
            const modal = new bootstrap.Modal(document.getElementById('jsonModal'));
            document.getElementById('jsonContent').textContent = '';
            document.getElementById('errorMessage')?.remove();
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
                    modal.show();
                    e.target.innerHTML = 'Done.';
                })
                .catch(error => {
                    console.error('Error:', error);
                    const errorMessage = document.createElement('div');
                    errorMessage.className = 'alert alert-danger';
                    errorMessage.id = 'errorMessage';
                    errorMessage.textContent = error.message;
                    document.getElementById('jsonContent').parentNode.insertBefore(errorMessage, document.getElementById('jsonContent'));
                    modal.show();
                    e.target.innerHTML = 'Try Again.';
                    e.target.disabled = false;
                    // alert('Error processing image');
                });
        }

        function scanToLaravel() {
            scanner.scan(displayImagesOnPage, {
                "output_settings": [{
                    "type": "return-base64",
                    "format": "jpg"
                }]
            });
        }

        function displayImagesOnPage(successful, mesg, response) {
            if (!successful) {
                console.error('Failed: ' + mesg);
                return;
            }

            console.log("Scanner Response Received");

            // 1. التأكد إن الـ response عبارة عن Object وليس String
            var data = (typeof response === 'string') ? JSON.parse(response) : response;

            console.log("Parsed Data:", data); // عشان نشوف الشكل النهائي بعد التحويل

            var base64Raw = null;

            // 2. محاولة استخراج الصورة بأكثر من طريقة (Flexible Extraction)
            if (data.output && data.output[0] && data.output[0].result && data.output[0].result[0]) {
                // المسار المتوقع من الـ Log اللي بعتيه
                base64Raw = data.output[0].result[0];
            } else if (data.images && data.images[0]) {
                // مسار بديل في بعض نسخ المكتبة
                base64Raw = data.images[0].src || data.images[0];
            }

            if (base64Raw) {
                // تنظيف البيانات من أي سطر جديد (\n) قد يسببه الـ Scanner
                base64Raw = base64Raw.replace(/\n/g, '');

                // إضافة الهيدر لو مش موجود
                var base64Image = base64Raw.startsWith('data:image')
                    ? base64Raw
                    : "data:image/jpeg;base64," + base64Raw;

                console.log("Image extracted successfully, starting upload...");
                uploadToServer(base64Image);
            } else {
                console.error("عذراً، لم نجد مصفوفة الصور داخل الـ Response. تأكدي من الـ Console log لرؤية الـ Structure.");
            }
        }

        function uploadToServer(base64Data) {

            fetch(`${baseUrl}/upload-scanned-image`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ image: base64Data })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // هنا السحر! بنقول للمتصفح يروح للرابط اللي بعته Laravel
                        window.location.href = data.redirect_url;
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
        }
    </script>

</body>

</html>
