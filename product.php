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

// Initialize variable to store products
$products_result = null;

// Check if search query is set
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    // Modify the query to fetch only matching products
    $products_query = "SELECT * FROM product WHERE ProductName LIKE '%$search%'";  
    $products_result = $conn->query($products_query);
} else {
    // Default query to fetch all products
    $products_query = "SELECT * FROM product";
    // Execute the query and store the result
    $products_result = $conn->query($products_query);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
      integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <title>Product</title>
    <style>
        /* Reset default margin and padding */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(to right, #fffdfd, #5cd7dd);
            font-family: Arial, sans-serif; /* Set default font family */
            overflow-y: auto;
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

        nav {
            background-color: #00caf7; /* Light blue navbar background */
            color: #fff; /* White text color */
            padding: 1rem; /* Padding around navbar */
            text-align: center; /* Center align navbar content */
            border-bottom: 2px solid black; /* Black border at the bottom */
        }

        .navbar-container {
            display: flex; /* Use flexbox for navbar layout */
            justify-content: space-between; /* Space items evenly */
            align-items: center; /* Center align items vertically */
        }

        .search-container {
            display: flex; /* Use flexbox for search container */
            align-items: center; /* Center align items vertically */
            justify-content: center; /* Center search input */
            flex: 1; /* Take remaining space in navbar */
        }

        .search-input {
            padding: 0.5rem; /* Padding inside search input */
            border: none; /* Remove default input border */
            border-radius: 0.3rem; /* Rounded input corners */
            margin-right: 0.5rem; /* Margin between input and button */
            width: 400px; /* Initial width for search input */
            border: 2px solid black; /* Black border around input */
        }

        .fas{
            padding: 8px;
        }

        .fas:hover {
        color: #5fd936;
      }

/* Main container styles */
/* Main container styles */
.main-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    margin-top: 20px;
}

/* Product box styles */
.product-box {
    width: 300px; /* Fixed width for the product box */
    margin-bottom: 20px;
    padding: 15px;
    border-top: 3px solid rgb(0, 0, 0);
    border-right: 3px solid black;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    background-color: white;
    transition: box-shadow 0.3s ease;
    box-sizing: border-box; /* Include padding and border in box size */
    display: flex; /* Use flexbox for product box content */
    flex-direction: column; /* Arrange content vertically */
    justify-content: space-between; /* Align items with space between them */
}

.product-box:hover {
    transform: translateY(-5px);
    background-color:navajowhite;
}

.product-box img {
    width: 100%; /* Set image width to 100% of its container (product box) */
    height: auto; /* Maintain aspect ratio */
    max-height: 200px; /* Set max height for the image */
    margin-bottom: 10px;
    border: 2px solid black;
    border-radius: 5px;
}

.product-box h3 {
    font-size: 18px;
    margin-bottom: 10px;
}

.product-box p {
    margin-bottom: 8px;
}

.product-box input[type="email"] {
    width: 100%; /* Full width for the email input minus padding and border */
    padding: 8px; /* Padding for the input */
    border: 2px solid black; /* Border for the input */
    border-radius: 3px; /* Border radius for the input */
    margin-bottom: 8px; /* Spacing below the input */
    box-sizing: border-box; /* Include padding and border in box size */
}

.buy-now-btn {
    width: 100%;
    padding: 8px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.buy-now-btn:hover {
    background-color: #0056b3;
    margin-top: auto; /* Align button at the bottom of the product box */
}


/* Responsive styles */
@media (max-width: 768px) {
    .product-box {
        max-width: 100%; /* Full width on smaller screens */
    }
}


        @media only screen and (max-width: 768px) {
            .search-input {
                width: 250px; /* Full width search input on smaller screens */
                border: 2px solid rgb(5, 5, 5); /* Dark border for input */
            }


            .navbar-container {
                justify-content: center; /* Center navbar items on smaller screens */
            }

            .logo {
                display: none; /* Hide logo on smaller screens */
            }

            body {
            background: linear-gradient(to right, #fffdfd, #5cd7dd);
            font-family: Arial, sans-serif; /* Set default font family */
            overflow-y: auto;
      }

        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="navbar-container">
                <div class="logo">
                    <img src="Wlogo.png" alt="logo" height="40px" />
                </div>
                <div class="search-container">
                    <!-- Search form in the navbar -->
                    <form method="GET" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <input type="text" class="search-input" name="search" placeholder ='Search products'/>
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </div>
        </nav>
    </header>
    <main>
        <div class="main-container">
            <!-- Display products based on search results -->
            <?php
            if ($products_result->num_rows > 0) {
                while ($row = $products_result->fetch_assoc()) {
                    echo "<div class='product-box'>";
                    echo "<img src='{$row['ProductImg']}' alt='{$row['ProductName']}' />";
                    echo "<h3>{$row['ProductName']}</h3>";
                    echo "<div style='background-color: #b0f380; padding: 5px; border: 1px solid black; border-radius: 5px; margin: 5px;'><p><b>Price:</b> &#x20B9; {$row['ProductPrice']}</p></div>";                 
                    echo "<div style='background-color: #b0f380;margin:5px;padding:5px;border: 1px solid black; border-radius: 5px;'><p><b>Seller Location:</b> {$row['SellerLocation']}</p></div>";
                    echo "<p style='max-height: 60px; overflow-y: auto;'>{$row['ProductDescription']}</p>";

                    echo "<form method='POST' action='buy_product.php'>";
                    echo "<input type='hidden' name='seller_email_id' value='{$row['SelleremailID']}' />";
                    echo "<input type='hidden' name='product_name' value='{$row['ProductName']}' />";
                    echo "<input type='email' name='customer_email' placeholder='Enter your email' required />";
                    echo "<button type='submit' class='buy-now-btn'>Buy Now</button>"; // Buy Now button
                    echo "</form>";
                    
                    echo "</div>";
                }
            } else {
                echo "<div style='display: flex; justify-content: center; align-items: center; height: 200px;'>";
                echo "<div style='padding: 20px; border: 2px solid black; border-radius: 5px;  background-color: rgb(215, 99, 99);'>";
                echo "No products found.";
                echo "</div>";
                echo "</div>";

            }
            ?>
        </div>
    </main>
</body>
</html>
