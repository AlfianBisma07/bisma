@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="text-center mb-4">
        <h1 class="display-4">Face Verification</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <video id="video" class="w-100" autoplay></video>
                    <div class="mt-3">
                        <button class="btn btn-primary" id="startButton">Start Verification</button>
                        <button class="btn btn-secondary" id="stopButton">Stop Verification</button>
                    </div>
                    <form id="uploadForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="image" id="imageInput" accept="image/*">
                        <button type="submit" class="btn btn-success mt-2">Verify Face</button>
                    </form>
                    <div id="result" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const video = document.getElementById('video');
    const startButton = document.getElementById('startButton');
    const stopButton = document.getElementById('stopButton');
    const uploadForm = document.getElementById('uploadForm');
    const resultDiv = document.getElementById('result');
    
    async function startVideo() {
        try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;
    } catch (err) {
        console.error("Error accessing the camera: ", err);
    }
}

function stopVideo() {
    const stream = video.srcObject;
    if (stream) {
        const tracks = stream.getTracks();
        tracks.forEach(track => track.stop());
        video.srcObject = null;
    }
}

startButton.addEventListener('click', startVideo);
stopButton.addEventListener('click', stopVideo);

uploadForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    
    const formData = new FormData(uploadForm);
    try {
        const response = await fetch('/verify-face', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        resultDiv.innerHTML = `<p>Number of faces detected: ${result.faces}</p>`;
    } catch (error) {
        console.error('Error:', error);
    }
});


</script>
@endsection
