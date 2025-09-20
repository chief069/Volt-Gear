<?php
// Database credentials
$servername = "127.0.0.1s";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password
$dbname = "voltgear";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch products
$sql = "SELECT `productID`, `productname`, `catId`, `price`, `ProductImage` FROM `product` WHERE 1";
$result = $conn->query($sql);

// Check if records exist
if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
                <th>ProductId</th>
                <th>ProductName</th>
                <th>Price</th>
                <th>Image</th>
       
            </tr>";
    // Output data for each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['ProductId']) . "</td>
                <td>" . htmlspecialchars($row['Product_Name']) . "</td>
                <td>" . htmlspecialchars($row['price']) . "</td>
                <td><img src='" . htmlspecialchars($row['ProductImage']) . "' width='50'></td>
         
              </tr>";
    }
    echo "</table>";
} else {
    echo "No products found.";
}

// Close connection
$conn->close();
?>
