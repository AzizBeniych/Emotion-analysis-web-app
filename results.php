<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emotion Analysis Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        img {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
        }
        .title {
            color: #333;
            margin-bottom: 30px;
        }
        .back-button {
            margin: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .back-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
    <button onclick="history.back()" class="back-button">Go Back</button>
        <h1 class="title">Emotion Analysis Results</h1>
        <?php
        session_start();

        if (isset($_SESSION['processedImagePath'], 
                  $_SESSION['emotionDistributionPath'],
                  $_SESSION['emotionHistogramPath'],
                  $_SESSION['faceSizesDistributionPath'],
                  $_SESSION['emotionProbabilityPath'],
                  $_SESSION['emotionCorrelationMatrixPath'])) {
            echo "<img src='" . $_SESSION['processedImagePath'] . "' alt='Processed Image'><br>";
            echo "<img src='" . $_SESSION['emotionDistributionPath'] . "' alt='Emotion Distribution'><br>";
            echo "<img src='" . $_SESSION['emotionHistogramPath'] . "' alt='Emotion Histogram'><br>";
            echo "<img src='" . $_SESSION['faceSizesDistributionPath'] . "' alt='Face Sizes Distribution'><br>";
            echo "<img src='" . $_SESSION['emotionProbabilityPath'] . "' alt='Emotion Probability'><br>";
            echo "<img src='" . $_SESSION['emotionCorrelationMatrixPath'] . "' alt='Emotion Correlation Matrix'><br>";
        } else {
            echo "<p>No results to display.</p>";
        }
        ?>
    </div>
</body>
</html>
