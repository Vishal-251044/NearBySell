<?php
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

$userDetails = "";
$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = sanitizeInput($_POST["login-email"]);
    $password = sanitizeInput($_POST["login-password"]);

    $seller_query = "SELECT * FROM Seller WHERE SelleremailID='$email' AND SellerPassword='$password'";
    $seller_result = $conn->query($seller_query);

    if ($seller_result === false) {
        $errorMsg = "Error executing seller query: " . $conn->error;
    } elseif ($seller_result->num_rows > 0) {
        $seller_row = $seller_result->fetch_assoc();
        $userDetails = "<div style='margin-bottom: 20px; font-size: 30px;'><b>Welcome, Seller!</b></div>";
        $userDetails .= "<div style='margin-bottom: 10px;'><b>Name:</b> " . $seller_row["SellerName"] . "</div>";
        $userDetails .= "<div style='margin-bottom: 10px;'><b>Email:</b> " . $seller_row["SelleremailID"] . "</div>";
        $userDetails .= "<div style='margin-bottom: 10px;'><b>Number:</b> " . $seller_row["SellerNumber"] . "</div>";
        $userDetails .= "<div style='margin-bottom: 10px;'><b>Location:</b> " . $seller_row["SellerLocation"] . "</div>";
        $userDetails .= "<div style='margin-bottom: 10px;'><b>Address:</b> " . $seller_row["SellerAddress"] . "</div>";
        $userDetails .= "<div style='margin-bottom: 10px;'><b>UPI ID:</b> " . $seller_row["SellerUPIID"] . "</div>";
        $userDetails .= "<div style='margin-bottom: 10px;background-color: rgba(220, 174, 99, 0.8); padding:10px;margin:15px; border: 2px solid black;border-radius: 5px;'><b>Note :</b> Sellers should not use the same product name because if you delete your product, all products with the same name will also be deleted.</div>";
        $userDetails .= "<button style='width: 120px; background-color: #4CAF50; color: white; padding: 8px 16px; margin-top: 15px; border: 2px solid black; border-radius: 5px; cursor: pointer; margin-right: 10px; font-size: 14px;' onclick='toggleAddProductForm()'>Add Product</button>";
        $userDetails .= "</div>";
        $userDetails .= "<div id='add-product-container' style='margin-top: 20px;'></div>";

// Fetch and display seller's products
$sellerEmail = $seller_row["SelleremailID"];
$products_query = "SELECT SelleremailID, ProductName, ProductPrice, ProductDescription FROM Product WHERE SelleremailID='$sellerEmail'";
$products_result = $conn->query($products_query);

if ($products_result === false) {
    $errorMsg = "Error fetching products: " . $conn->error;
} else {
    $userDetails .= "<div id='seller-products-container' style='margin-top: 20px;'>";
    while ($product_row = $products_result->fetch_assoc()) {
        $userDetails .= "<div class='product-item' style='background-color: #66CDAA; border: 2px solid black;border-radius: 5px; padding: 10px; margin-bottom: 10px;'>";
        $userDetails .= "<b>Name:</b> " . $product_row["ProductName"] . "<br>";
        $userDetails .= "<b>Price:</b> $" . $product_row["ProductPrice"] . "<br>";
        $userDetails .= "<b>Description:</b> " . $product_row["ProductDescription"] . "<br>";
        $productId = $product_row["SelleremailID"] . '_' . $product_row["ProductName"];
        $userDetails .= "<button class='remove-product-btn' style='background-color: #FF6347; color: white; padding: 5px 10px; border: 2px solid black;border-radius: 5px; margin-top:5px; cursor: pointer;' data-product-id='{$productId}'>Remove</button>";
        $userDetails .= "</div>";
    }    
    $userDetails .= "</div>";
}


    } else {
        $customer_query = "SELECT * FROM Customer WHERE CustomeremailID='$email' AND CustomerPassword='$password'";
        $customer_result = $conn->query($customer_query);

        if ($customer_result === false) {
            $errorMsg = "Error executing customer query: " . $conn->error;
        } elseif ($customer_result->num_rows > 0) {
            $customer_row = $customer_result->fetch_assoc();
            $userDetails = "<div style='margin-bottom: 20px; font-size: 30px;'><b>Welcome, Customer!</b></div>";
            $userDetails .= "<div style='margin-bottom: 10px;'><b>Name:</b> " . $customer_row["CustomerName"] . "</div>";
            $userDetails .= "<div style='margin-bottom: 10px;'><b>Email:</b> " . $customer_row["CustomeremailID"] . "</div>";
            $userDetails .= "<div style='margin-bottom: 10px;'><b>Number:</b> " . $customer_row["CustomerNumber"] . "</div>";
            $userDetails .= "<div style='margin-bottom: 10px;'><b>Location:</b> " . $customer_row["CustomerLocation"] . "</div>";
            $userDetails .= "<div style='margin-bottom: 10px;'><b>Address:</b> " . $customer_row["CustomerAddress"] . "</div>";
            $userDetails .= "<div style='margin-bottom: 10px;background-color: rgba(220, 174, 99, 0.8); padding:10px;margin:15px; border: 2px solid black;border-radius: 5px;'><b>Note :</b> Customers can conveniently purchase products by directly contacting sellers through the contact information they provide, which streamlines communication and facilitates smooth transactions.</div>";

        } else {
            $errorMsg = '<script>alert("Unknown user, please check your email and password.");</script>';
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'remove_account') {
        $email = sanitizeInput($_POST["login-email"]);
        $password = sanitizeInput($_POST["login-password"]);

        // Check if the email and password belong to a seller
        $seller_query = "SELECT * FROM Seller WHERE SelleremailID='$email' AND SellerPassword='$password'";
        $seller_result = $conn->query($seller_query);

        if ($seller_result === false) {
            $errorMsg = "Error executing seller query: " . $conn->error;
        } elseif ($seller_result->num_rows > 0) {
            // Remove the seller account
            $remove_seller_query = "DELETE FROM Seller WHERE SelleremailID='$email'";
            if ($conn->query($remove_seller_query) === TRUE) {
                $userDetails = '<script>alert("Seller account removed successfully!");window.history.back();</script>';
            } else {
                $errorMsg = "Error removing seller account: " . $conn->error;
            }
        } else {
            // Check if the email and password belong to a customer
            $customer_query = "SELECT * FROM Customer WHERE CustomeremailID='$email' AND CustomerPassword='$password'";
            $customer_result = $conn->query($customer_query);

            if ($customer_result === false) {
                $errorMsg = "Error executing customer query: " . $conn->error;
            } elseif ($customer_result->num_rows > 0) {
                // Remove the customer account
                $remove_customer_query = "DELETE FROM Customer WHERE CustomeremailID='$email'";
                if ($conn->query($remove_customer_query) === TRUE) {
                    $userDetails = '<script>alert("Customer account removed successfully!");window.history.back();</script>';
                } else {
                    $errorMsg = "Error removing customer account: " . $conn->error;
                }
            } else {
                $errorMsg = '<script>alert("Invalid email or password.");window.history.back();</script>';
            }
        }
    }
}


$conn->close();
?>

 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
     body {
    font-family: Arial, sans-serif;
    background-image: url(b8.jpg);
    background-size: cover;
    background-position: center; 
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh; 
}

#login-form {
    background-color: rgba(235, 221, 74, 0.7);
    padding: 20px;
    margin-top: 40px;
    border-radius: 20px;
    border: 3px solid black;
    max-width: 400px;
    width: 100%;
    height: calc(100vh - 140px); /* Adjusted height */
    overflow-y: auto; /* Added overflow for vertical scrolling */
}


#user-details-container {
    max-height: calc(100vh - 140px); /* Adjusted max-height */
    overflow-y:auto; /* Added overflow for vertical scrolling */
}


::-webkit-scrollbar {
    width: 12px; 
}

::-webkit-scrollbar-track {
    background: white;
    border: 2px solid black;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background-color: #888; 
    border-radius: 10px;
    border: 3px solid black; 
}

::-webkit-scrollbar-thumb:hover {
    background-color: black; 
}

@media screen and (max-width: 768px) {
    #login-form {
        margin: 10px; /* Adjusted margin-top for smaller screens */
        height: calc(100vh - 180px); /* Adjusted height for smaller screens */
    }

    #user-details-container {
        max-height: calc(100vh - 180px); /* Adjusted max-height for smaller screens */
    }

    body {
    font-family: Arial, sans-serif;
    background: linear-gradient(to right, #fffdfd, #c05fc5);
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh; 
}
}

@media screen and (max-width: 480px) {
    #login-form {
        margin: 10px; /* Adjusted margin-top for smaller screens */
        height: calc(100vh - 220px); /* Adjusted height for smaller screens */
    }

    #user-details-container {
        max-height: calc(100vh - 220px); /* Adjusted max-height for smaller screens */
    }
}



        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 2px solid black;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button.btn {
            display: block;
            width: 100%;
            padding: 10px;
            border: 1px solid black;
            border-radius: 5px;
            background-color: #007bff;
            color: #000000;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }
        button.but {
            display: block;
            width: 100%;
            padding: 10px;
            border: 1px solid black;
            border-radius: 5px;
            background-color: red;
            color: #000000;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }

        button.btn:hover {
            background-color: #0056b3;
        }

        #add-product-form {
            display: none;
            margin-top: 20px;
            padding: 10px;
            border: 2px solid black;
            border-radius: 5px;
            background-color: azure;
          }

          input[type="text"],
         input[type="number"],
        textarea {
        width: 100%;
        padding: 6px;
        border: 1px solid #ccc;
        border-radius: 3px;
        box-sizing: border-box;
    }

    </style>
</head>
<body>
<div id="login-form">
    <?php
    if (empty($userDetails)) {
    ?>
    <h2>Login</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group">
            <label for="login-email">Email:</label>
            <input type="email" id="login-email" name="login-email" required />
        </div>
        <div class="form-group">
            <label for="login-password">Password:</label>
            <input type="password" id="login-password" name="login-password" required />
        </div>
        <button type="submit" class="btn">Login</button>
        <button type="Submit" class="but" name="action" value="remove_account">Remove account</button>
        <div class="text" style="background-color: rgba(158, 213, 223, 0.2); border: 2px solid black; border-radius:5px;padding:15px; margin-top: 20px;"><b>Note :</b> To log in securely, make sure to enter your details carefully and accurately. If you accidentally touch the 'remove account' option, please be aware that your account will be permanently deleted from the database, and recovery may not be possible.</div>


    </form>
    <?php
    } else {
        echo '<div id="user-details-container">' . $userDetails . '</div>'; // Added the user details container
    }
    if (!empty($errorMsg)) {
        echo $errorMsg;
    }
    ?>
</div>

    <!-- Add Product form -->
    <div id="add-product-form" style="display: none;">
    <form method="post" action="process_add_product.php" enctype="multipart/form-data">
        <!-- Hidden input fields for seller's email and location -->
        <input type="hidden" id="seller-email" name="seller-email" value="<?php echo isset($seller_row) ? $seller_row['SelleremailID'] : ''; ?>" />
        <input type="hidden" id="seller-location" name="seller-location" value="<?php echo isset($seller_row) ? $seller_row['SellerLocation'] : ''; ?>" />

        <div class="form-group">
            <label for="product-name">Product Name:</label>
            <input type="text" id="product-name" name="product-name" required />
        </div>
        <div class="form-group">
            <label for="product-price">Product Price:</label>
            <input type="number" id="product-price" name="product-price" required />
        </div>
        <div class="form-group">
            <label for="product-description">Product Description:</label>
            <textarea id="product-description" name="product-description" required></textarea>
        </div>
        <div class="form-group">
            <label for="product-image">Product Image:</label>
            <input type="file" id="product-image" name="product-image" accept="image/*" required />
        </div>
        <!-- Seller's email and location will be filled in automatically -->
        <div class="form-group">
            <label for="seller-email">Seller Email:</label>
            <input type="email" id="seller-email-display" value="<?php echo isset($seller_row) ? $seller_row['SelleremailID'] : ''; ?>" readonly />
        </div>
        <div class="form-group">
            <label for="seller-location">Seller Location:</label>
            <input type="text" id="seller-location-display" value="<?php echo isset($seller_row) ? $seller_row['SellerLocation'] : ''; ?>" readonly />
        </div>
        <button type="submit" class="btn">Add Product</button>
    </form>
</div>

<script>
    function toggleAddProductForm() {
        var formContainer = document.getElementById('add-product-form');
        var userDetailsContainer = document.getElementById('user-details-container');
        if (formContainer.style.display === 'none') {
            formContainer.style.display = 'block';
            userDetailsContainer.appendChild(formContainer);
        } else {
            formContainer.style.display = 'none';
            document.body.appendChild(formContainer);
        }
    }

    document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-product-btn')) {
        var productId = e.target.getAttribute('data-product-id');
        var productIdParts = productId.split('_');
        var sellerEmail = productIdParts[0];
        var productName = productIdParts[1];
        if (confirm('Are you sure you want to remove this product?')) {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = xhr.responseText;
                    if (response === 'success') {
                        e.target.parentNode.remove();
                    } else {
                        alert('Error removing product: ' + response);
                    }
                }
            };
            xhr.open('POST', 'remove_product.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.send('seller_email=' + encodeURIComponent(sellerEmail) + '&product_name=' + encodeURIComponent(productName));
        }
    }
});

document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('login-form').addEventListener('Submit', function (e) {
            e.preventDefault(); // Prevent the default form submission
            var formData = new FormData(this);

            // Send the form data asynchronously using Fetch API
            fetch('<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('user-details-container').innerHTML = data;
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });

</script>

</body>
</html>


</body>
</html>
