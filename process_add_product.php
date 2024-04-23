<?php
// Establish database connection
$db_host = 'localhost';
$db_user = 'root';
$db_password = '';
$db_name = 'sellease';

$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function sanitizeInput($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data); 
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $productName = sanitizeInput($_POST["product-name"]);
    $productPrice = sanitizeInput($_POST["product-price"]);
    $productDescription = sanitizeInput($_POST["product-description"]);
    $sellerEmail = sanitizeInput($_POST["seller-email"]);
    $sellerLocation = sanitizeInput($_POST["seller-location"]);

    // Check if the directory exists, otherwise create it
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true); // Create the directory with proper permissions
    }

    // File upload handling
    $targetFile = $uploadDir . basename($_FILES["product-image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if file was uploaded successfully
    if ($_FILES["product-image"]["error"] !== UPLOAD_ERR_OK) {
        echo '<script>alert("Sorry, there was an error uploading your file.");window.history.back();</script>';
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["product-image"]["size"] > 5000000) {
        echo '<script>alert("Sorry, your file is too large.");window.history.back();</script>';
        $uploadOk = 0;
    }

    // Allow certain file formats
    $allowedExtensions = array("jpg", "jpeg", "png", "gif");
    if (!in_array($imageFileType, $allowedExtensions)) {
        echo '<script>alert("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");window.history.back();</script>';
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo '<script>alert("Sorry, your file was not uploaded.");window.history.back();</script>';
    } else {
        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES["product-image"]["tmp_name"], $targetFile)) {
            // File uploaded successfully, now insert product details into database
            $productImg = $targetFile; // Set the product image path
            $insertQuery = "INSERT INTO product (SelleremailID, ProductName, ProductDescription, ProductPrice, ProductImg, SellerLocation) 
                            VALUES ('$sellerEmail', '$productName', '$productDescription', '$productPrice', '$productImg', '$sellerLocation')";

            if ($conn->query($insertQuery) === TRUE) {
                echo '<script>alert("Product added successfully.");window.history.back();</script>';
            } else {
                echo '<script>alert("Error adding product: ' . $conn->error . '");window.history.back();</script>';   
            }
        } else {
            echo '<script>alert("Sorry, there was an error uploading your file.");window.history.back();</script>'; 
        }
    }
}

$conn->close();
?>
