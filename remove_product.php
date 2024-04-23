<?php
$db_host = 'localhost';
$db_user = 'root';
$db_password = '';
$db_name = 'sellease';

$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["seller_email"]) && isset($_POST["product_name"])) {
    $sellerEmail = $conn->real_escape_string($_POST["seller_email"]);
    $productName = $conn->real_escape_string($_POST["product_name"]);

    $delete_query = "DELETE FROM Product WHERE SelleremailID='$sellerEmail' AND ProductName='$productName'";
    $delete_result = $conn->query($delete_query);

    if ($delete_result === true) {
        echo 'success'; // Send success response to JavaScript
    } else {
        echo 'Error removing product: ' . $conn->error; // Send error response to JavaScript
    }
}

$conn->close();
?>
