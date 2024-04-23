<?php
// Database connection code here
$db_host = 'localhost';
$db_user = 'root';
$db_password = '';
$db_name = 'sellease';
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the seller email ID, product name, and customer email from the form
    $seller_email_id = $_POST['seller_email_id'];
    $product_name = $_POST['product_name'];
    $customer_email = $_POST['customer_email'];

    // Validate and sanitize inputs as needed

    // Query to check if the customer email exists
    $customer_query = "SELECT * FROM Customer WHERE CustomeremailID = '$customer_email'";
    $customer_result = $conn->query($customer_query);

    if (!$customer_result) {
        die("Query failed: " . $conn->error);
    }

    if ($customer_result->num_rows > 0) {
        $customer_row = $customer_result->fetch_assoc();
        $customer_location = $customer_row['CustomerLocation'];

        // Query to get product and seller details
        $product_query = "SELECT * FROM Product WHERE SelleremailID = '$seller_email_id' AND ProductName = '$product_name'";
        $product_result = $conn->query($product_query);

        if (!$product_result) {
            die("Query failed: " . $conn->error);
        }

        if ($product_result->num_rows > 0) {
            $product_row = $product_result->fetch_assoc();
            $seller_location = $product_row['SellerLocation'];

            if ($customer_location == $seller_location) {
                // Locations match, fetch seller details from Seller table
                $seller_query = "SELECT * FROM Seller WHERE SelleremailID = '$seller_email_id'";
                $seller_result = $conn->query($seller_query);
                if ($seller_result->num_rows > 0) {
                    $seller_row = $seller_result->fetch_assoc();
                    $seller_phone = '+91' . $seller_row['SellerNumber']; 
                    echo "<div style='display: flex; justify-content: center; align-items: center; height: 100vh;'>";
                    echo "<div style='padding: 50px;margin:50px; border: 3px solid black; border-radius: 5px; text-align: center; background-color:#F9E795'>";
                    echo "<div style='font-weight: bold; margin-bottom: 10px;'><h2>Seller Details:</h2></div>";
                    echo "<div><b>Seller Name: </b>" . $seller_row['SellerName'] . "</div>";
                    echo "<div><b>Seller Email: </b>" . $seller_email_id . "</div>";
                    echo "<div><b>Seller Phone: </b>" . $seller_phone . "</div>"; 
                    echo "<div><b>Seller Location: </b>" . $seller_location . "</div>";
                    echo "<div><b>Seller UPI ID: </b>" . $seller_row['SellerUPIID'] . "</div><br>";


                    $seller_phone = $seller_row['SellerNumber']; // Assuming the phone number is stored without the country code

                    echo "<div style='background-color: rgb(4, 247, 243); border: 2px solid black; padding: 10px; border-radius: 15px;'><a href='https://wa.me/91$seller_phone' style='text-decoration: none; color: inherit;'><strong style='text-decoration: none;'>Contact seller on WhatsApp</strong></a></div>";
                                        
                    
                    echo "</div>";
                    echo "</div>";
                }
                            
                
                 else {
                    echo '<script>alert("Seller details not found.");window.history.back();</script>'; 
                }
            } else {
                echo '<script>alert("Seller does not sell in your location.");window.history.back();</script>'; 
            }
        } else {
            echo '<script>alert("Product not found.");window.history.back();</script>'; 
        }
    } else {
        echo '<script>alert("Customer not found. First you login as customer.");window.history.back();</script>'; 
    }
}
?>
