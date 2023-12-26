
<?php
// Start the session
session_start();

// Include the AWS SDK for PHP library
require 'vendor/autoload.php';

// Set your AWS access key and secret key
putenv('AWS_ACCESS_KEY_ID=your_aws_access_key_id');
putenv('AWS_SECRET_ACCESS_KEY=your_aws_secret_access_key');

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

// Set up AWS S3 Client
$s3 = new S3Client([
    'region'  => 'your_aws_region', // e.g., 'us-east-1'
    'version' => 'latest'
]);

function analyzeImageWithPython($localImagePath, $outputPath) {
    // Replace the following paths with the correct ones for your server environment
    $pythonPath = "path_to_python_executable"; // e.g., "/usr/bin/python3"
    $scriptPath = "path_to_python_script"; // e.g., "/var/www/html/Sentimeter-Algorithm.py"

    // Construct the command without using escapeshellcmd
    $command = ""$pythonPath" "$scriptPath" "$localImagePath" "$outputPath" 2>&1";
    exec($command, $output, $return_var);

    if ($return_var !== 0) {
        foreach ($output as $line) {
            echo htmlspecialchars($line) . "<br>";
        }
        return false;
    } else {
        return true;
    }
}

function processImage($imageName, $bucketName) {
    global $s3;

    // Replace with the actual path where the image will be temporarily stored
    $localImagePath = 'path_to_temporary_storage' . $imageName; // e.g., '/tmp/' . $imageName
    // Replace with the actual path where the output will be saved
    $outputPath = 'path_to_output_directory'; // e.g., '/var/www/html/Outputs'

    try {
        // Download the file from S3 to a local path
        $result = $s3->getObject([
            'Bucket' => $bucketName,
            'Key'    => $imageName,
            'SaveAs' => $localImagePath
        ]);

        // Analyze the image using the Python script
        if (analyzeImageWithPython($localImagePath, $outputPath)) {
            // Store results information in session variables
            $_SESSION['processedImagePath'] = 'Outputs/processed_image.jpg';
            $_SESSION['emotionDistributionPath'] = 'Outputs/emotion_distribution.png';
            $_SESSION['emotionHistogramPath'] = 'Outputs/emotion_histogram.png';
            $_SESSION['faceSizesDistributionPath'] = 'Outputs/face_sizes_distribution.png';
            $_SESSION['emotionProbabilityPath'] = 'Outputs/emotion_probability.png';
            $_SESSION['emotionCorrelationMatrixPath'] = 'Outputs/emotion_correlation_matrix.png';

            // Redirect to results page
            header('Location: results.php');
            exit();
        }

    } catch (S3Exception $e) {
        echo "Error downloading image from S3: " . $e->getMessage();
    }
}

// Example usage (this line should be called appropriately based on your application logic)
// processImage($uploadedImageName, $bucketName);
?>
