<!DOCTYPE html>
<html>
<head>
    <title>Scan Passport</title>

    <style>
        body {
            margin: 0;
            background: #000;
            color: #fff;
            text-align: center;
        }

        video {
            width: 100%;
            height: 100vh;
            object-fit: cover;
        }

        /* 🔥 Overlay زي البنوك */
        .overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 80%;
            height: 250px;
            transform: translate(-50%, -50%);
            border: 3px solid #00ff99;
            border-radius: 10px;
        }

        .scan-btn {
            position: absolute;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            padding: 15px 30px;
            font-size: 18px;
            background: #00ff99;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
    </style>
</head>

<body>

<video id="video" autoplay></video>
<div class="overlay"></div>

<button class="scan-btn" onclick="scan()">Scan</button>
@vite('resources/js/app.js')

</body>
</html>
