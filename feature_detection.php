<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emotion Detection</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        nav {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            text-align: center;
        }

        .container {
            display: flex;
            padding: 20px;
            gap: 20px;
        }

        .sidebar {
            background: rgba(5, 17, 164, 0.1);
            width: 250px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
        }

        .sidebar h2 {
            color: rgb(5, 17, 164);
            text-align: center;
            margin-bottom: 15px;
        }

        .sidebar ul {
            display: flex;
            flex-direction: column;
            padding: 0;
            margin: 0;
            list-style-type: none;
        }

        .sidebar ul li {
            background-color: rgba(5, 17, 164, 0.2);
            color: rgb(5, 17, 164);
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 3px;
            transition: background-color 0.3s ease;
        }

        .sidebar ul li:hover {
            background-color: rgb(5, 17, 164);
            color: white;
        }

        .camera-container {
            flex-grow: 1;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
        }

        .camera-container h2 {
            color: rgb(5, 17, 164);
            margin-bottom: 20px;
            font-size: 24px;
        }

        .camera-container video {
            width: 50%;
            border-radius: 10px;
            border: 2px solid rgb(5, 17, 164);
            background: black;
        }
        .images li{
            margin-left:10px;
        }
        .images li img{
            height: 20px;
            width: 20px;
            transform: scale(2.5);
            transition: transform 0.3s ease; 
            display:inline;
        } 
    </style>
</head>
<body>
    <?php include 'navbar.html'; ?>
    <div class="container">
        <div class="sidebar">
            <h2>Emotional Features</h2>
            <ul class="images">
                <li>Happy</li>
                <li>Sad</li>
                <li>Angry</li>
                <li>Surprise</li>
                <li>Neutral</li>
                <li>Fear</li>
                <li>Disgust</li>
            </ul>
        </div>
        <div class="camera-container">
            <h2>Show Your Face</h2>
            <img id="cameraFeed" src="http://127.0.0.1:5000/video_feed" alt="Emotion Detection Stream">
            <h3 id="emotionResult" class="emotion-text">Emotion: Loading...</h3>
        </div>
    </div>
    <script>
        async function fetchEmotionData() {
            try {
                const response = await fetch('http://127.0.0.1:5000/emotion'); // Fetch emotion data from Flask
                if (response.ok) {
                    const data = await response.json();
                    document.getElementById('emotionResult').innerText = `Emotion: ${data.emotion}`;
                } else {
                    console.error('Error fetching emotion data:', response.statusText);
                }
            } catch (error) {
                console.error('Error fetching emotion data:', error);
            }
        }

        // Fetch emotion data every 2 seconds
        setInterval(fetchEmotionData, 2000);
    </script>
</body>
</html>