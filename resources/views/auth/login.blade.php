<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .login-container h2 {
            margin-bottom: 1.5rem;
        }
        .alert {
            display: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="text-center">Login</h2>
        <form id="loginForm" method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        {{-- <div class="alert alert-danger mt-3" role="alert" id="alert">Face not detected. Please try again.</div> --}}

        <!-- Elemen video dan canvas untuk capture gambar -->
        <video id="video" width="640" height="480" autoplay style="display: none;"></video>
        <canvas id="canvas" width="640" height="480" style="display: none;"></canvas>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const loginForm = document.getElementById('loginForm');

            async function startVideo() {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                    video.srcObject = stream;

                    // Automatically capture image after 5 seconds
                    setTimeout(captureImage, 5000); // 5000 milliseconds = 5 seconds
                } catch (err) {
                    console.error("Error accessing the camera: ", err);
                }
            }

            function captureImage() {
                const context = canvas.getContext('2d');
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                canvas.toBlob(async (blob) => {
                    const formData = new FormData();
                    formData.append('image', blob, 'face.jpg');

                    try {
                        const response = await fetch('http://localhost:5000/verify', {
                            method: 'POST',
                            body: formData,
                        });

                        const result = await response.json();
                        if (result.faces > 0) {
                            loginForm.submit(); // Submit the form if face detected
                        } else {
                            alert('Face not detected. Please try again.');
                            setIner(captureImage, 5000);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                    }
                });
            }

            startVideo();
        });
    </script>
</body>
</html>
