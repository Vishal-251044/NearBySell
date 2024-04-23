<?php
if (isset($_POST['create_account'])) {
    // Retrieve form data
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];
    $location = $_POST['location'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $accountType = $_POST['accountType']; // This will be either "seller" or "customer"

    // Database connection
    $host = "localhost"; // Change to your database host
    $username = "root"; // Change to your database username
    $db_password = ""; // Change to your database password
    $database = "sellease"; // Change to your database name

    $conn = new mysqli($host, $username, $db_password, $database);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if email already exists
    $check_email_sql = "SELECT * FROM customer WHERE CustomerEmailID = ?";
    $check_email_stmt = $conn->prepare($check_email_sql);
    if (!$check_email_stmt) {
        die("Error in preparing statement: " . $conn->error);
    }
    $check_email_stmt->bind_param("s", $email);
    $check_email_stmt->execute();
    $result = $check_email_stmt->get_result();

    if ($result->num_rows > 0) {
        // Email already exists, show error message
        echo '<script>alert("Error: Email already exists in the database.");window.history.back();</script>';
    } else {
        // Email does not exist, proceed with account creation based on account type
        if ($accountType == "seller") {
            $upi = $_POST['upi']; // Only for sellers
            $insert_sql = "INSERT INTO seller (SellerName, SellerNumber, SellerEmailID, SellerLocation, SellerAddress, SellerPassword, SellerUPIID)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            if (!$stmt) {
                die("Error in preparing statement: " . $conn->error);
            }
            $stmt->bind_param("sssssss", $name, $mobile, $email, $location, $address, $password, $upi);
        } elseif ($accountType == "customer") {
            $insert_sql = "INSERT INTO customer (CustomerName, CustomerNumber, CustomerEmailID, CustomerLocation, CustomerAddress, CustomerPassword)
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            if (!$stmt) {
                die("Error in preparing statement: " . $conn->error);
            }
            $stmt->bind_param("ssssss", $name, $mobile, $email, $location, $address, $password);
        } else {
            echo '<script>alert("Invalid account type.");window.history.back();</script>';
            exit;
        }

        if ($stmt->execute()) {
            // Success message
            echo '<script>alert("Account created successfully!");window.history.back();</script>';
        } else {
            // Error message
            echo '<div>Error: ' . $stmt->error . '</div>';
        }

        $stmt->close();
    }

    $conn->close();
} else {
    echo '<script>alert("Error: Form data not submitted.");window.history.back();</script>';
}
?>
