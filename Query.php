
<?php
// Include the AWS SDK for PHP library
require 'vendor/autoload.php';
require 'processImage.php';

// Set your AWS access key and secret key
putenv('AWS_ACCESS_KEY_ID=your_aws_access_key_id');
putenv('AWS_SECRET_ACCESS_KEY=your_aws_secret_access_key');

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

// Set the S3 bucket name and the path where the image will be stored
$bucket_name = 'your_s3_bucket_name';
$bucket_path = 'your_s3_bucket_path'; // e.g., 'https://s3.region.amazonaws.com/your_bucket_name'

// Initialize message variable
$message = '';
$message_false = '';

// Check if the form was submitted
if (isset($_POST["submit"])) {
    // Get the image file and its attributes
    $file = $_FILES["file"];
    $filename = $file["name"];
    $filetype = $file["type"];
    $filetemp = $file["tmp_name"];
    $filesize = $file["size"];

    // Check if the file is an image
    $allowed_types = array("image/jpeg", "image/png", "image/gif");
    if (!in_array($filetype, $allowed_types)) {
        $message_false = "*Sorry, only JPEG, PNG, and GIF files are allowed.";
    } else {
        // Set up the S3 client
        $s3 = new S3Client([
            'region' => 'your_aws_region', // e.g., 'us-east-1'
            'version' => 'latest'
        ]);

        // Generate a unique file name for the image
        $new_filename = uniqid() . '_' . $filename;

        try {
            // Upload the image to S3
            $result = $s3->putObject([
                'Bucket' => $bucket_name,
                'Key' => $new_filename,
                'Body' => fopen($filetemp, 'r'),
                'ContentType' => $filetype
            ]);

            $uploadSuccessful = true;
            // Display a success message
            $message = "File uploaded successfully.";

        } catch (S3Exception $e) {
          $uploadSuccessful = false;
            // Display an error message
            $message_false = "Error uploading image: " . $e->getMessage();
        }
    }
    if ($uploadSuccessful) {
      $uploadedImageName = $new_filename;
      $bucketName = $bucket_name;
      processImage($uploadedImageName, $bucketName);
    }
}
// Display messages
if ($message_false) {
  echo "<p style='color:red;'>$message_false</p>";
} else {
  echo "<p style='color:green;'>$message</p>";
}

?>


<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sentimeter</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<!-- Bootstrap icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <style>
    .gradient-custom-3 {
      /* fallback for old browsers */
      background: #84fab0;

      /* Chrome 10-25, Safari 5.1-6 */
      background: linear-gradient(to right, rgba(255, 255, 153, 1), rgba(255, 204, 0, 1));

      /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
      background: linear-gradient(to right, rgba(255, 255, 153, 1), rgba(255, 204, 0, 1));
    }

    .gradient-custom-4 {
      /* fallback for old browsers */
      background: #84fab0;

      /* Chrome 10-25, Safari 5.1-6 */
      background: linear-gradient(to right, rgba(255, 255, 153, 1), rgba(255, 204, 0, 1));

      /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
      background: linear-gradient(to right, rgba(255, 255, 153, 1), rgba(255, 204, 0, 1));
    }
    
		h1{
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
        }
        footer{
            position: fixed;
            bottom: 0;
            width:100%;
        }
        .message-box {
            text-align: center;
			color:green;
			font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
        }
        .message_false{
            text-align: center;
			color:red;
			font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
        }
        .form-control {
    display: block;
    width: 36%;
    margin-left: 33%;
    padding: 0.375rem 0.75rem;
    /* font-size: 1rem; */
    /* font-weight: 400; */
    line-height: 1.5;
    color: linear-gradient(to right, rgba(255, 255, 153, 1), rgba(255, 204, 0, 1));
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background-color: var(--bs-body-bg);
    background-clip: padding-box;
    border: var(--bs-border-width) solid var(--bs-border-color);
    border-radius: var(--bs-border-radius);
    transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}
button{
    margin-top: 8px;
}
span {
    color: #343a40!important;
    font-size: 24px;
    /* margin-top: 71px; */
    position: relative;
    top: -20;
}
.bi-upload::before {
    content: "\f603";
    color:#343a40!important;
}
.navbar{
    position: fixed;
    top: 0;
    width: 100%;
}
h6{
    margin-top: 121px;
}
  </style>


</head>

<body>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
    crossorigin="anonymous"></script>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light gradient-custom-3">
    <!-- Container wrapper -->
    <div class="container-fluid">
      <!-- Navbar brand -->
      <a class="navbar-brand" href="index.html">
        <div class="d-flex align-items-center">
          <h3>Emotions</h3>
        </div>
      </a>
      <!-- Toggler/collapsible Button -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <!-- Nav elements -->
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link" href="index.html">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="Query.php"><u><b>Query</b></u></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="About.html">About</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="Contact.html">Contact</a>
          </li>
          <!-- User actions -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userActionsDropdown" role="button"
              data-bs-toggle="dropdown" aria-expanded="false">
              <img src="Images/person-lines-fill.svg" height="25" alt="Person icon with lines" loading="lazy" />
            </a>
            <ul class="dropdown-menu" aria-labelledby="userActionsDropdown">
              <li><a class="dropdown-item" href="Register.html">Register</a></li>
              <li><a class="dropdown-item" href="EditAccount.html">Edit Account</a></li>
              <li><a class="dropdown-item" href="ViewHistory.html">View History</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container-fluid">

    <!-- Top row (tutorial) -->
    <div class="row text-center">
      <div class="col ms-4 mb-4">
        <br/>
        
        <br/>
 
        <h6><span>Upload Image for Analysis</span><br>Welcome to our image analysis service. Upload your image, and we'll provide insights related to emotions.
        <br/>
        Your privacy is our priority. Your data will only be used for the intended purpose and will not be shared without your consent.</h6>
      </div>
      <div class="row text-center">
      <div class="col ms-4 mb-4">
        <a href="#" style="text-decoration: none;"><i class="bi bi-upload" style="font-size: 6rem; color:lightseagreen"></i></a>
      </div>
    </div>
      <form action="Query.php" method="post" enctype="multipart/form-data">
		  <label for="imageInput" class="form-label">Drag or Select an Image file:</label>
        <input type="file" class="form-control" id="imageInput" name="file" accept="image/*">
        <div class="message-box">
        <?php echo $message; ?>
    </div>
    <div class="message_false">
        <?php echo $message_false; ?>
    </div>
        <button type="submit" name="submit" class="btn large gradient-custom-4">Upload Image</button>
    </form>
    </div>
    <!-- Bottom row(File upload) -->
    <div class="row">
      
    </div>

  </div>


    


  <!-- Footer -->
  <footer class="gradient-custom-3" style="padding-top: 20px;">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <p style="color: black;">&#169; 2024 Sentimeter. All rights reserved.</p>
        </div>
        <div class="col-md-6 text-end">
          <a href="tel://97338482200" class="me-3" style="text-decoration: none;">
            <img src="Images/telephone-inbound-fill.svg" height="25" alt="Phone icon" loading="lazy" />
          </a>
          <a href="mailto:Sentimeter.bh@gmail.com" class="me-3" style="text-decoration: none;">
            <img src="Images/envelope-at-fill.svg" height="25" alt="Email icon" loading="lazy" />
          </a>
          <a href="#" class="me-3" style="text-decoration: none;">
            <img src="Images/instagram.svg" height="25" alt="Instagram icon" loading="lazy" />
          </a>
          <a href="Contact.html" style="text-decoration: none;">
            <img src="Images/geo-alt-fill.svg" height="25" alt="Location icon" loading="lazy" />
          </a>
        </div>
      </div>
    </div>
  </footer>

</body>

</html>


