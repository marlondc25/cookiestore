<?php
ob_start(); 
session_start();
if (!isset($_SESSION['username'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}
include 'db_connect.php';

// Get the username from the session
$username = $_SESSION['username'];

// Logout functionality
if (isset($_POST['logout'])) {
    // Unset all of the session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to the login page after logout
    header("Location: order_form.php");
    exit();
}
ob_end_flush(); // Flush the output buffer
?>
<!DOCTYPE html>
<html lang="en">
<head>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>

<link href="https://fonts.cdnfonts.com/css/mastery-kingdom" rel="stylesheet">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Buddies</title>
    <style>
            

        
        body {
            font-family: Arial, sans-serif;
            background: url('your-background-image2.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            font-family: 'Italic', sans-serif;
        }

        .wrapper {
            display: flex;
            justify-content: center;
        }

        .container {
            max-width: 400px; /* Adjust as needed */
            margin: 20px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            font-family: 'Mastery Kingdom', sans-serif;
            overflow-x: auto;
            background-color: #fff; /* Initial background color with alpha (transparency) */
            transition: background-color 0.5s ease; /* Transition effect for background-color property */
        }

        .orderscontainer {
            max-width: 1200px; /* Adjust as needed */
            margin: 20px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            font-family: 'Mastery Kingdom', sans-serif;
            overflow-x: auto;
            background-color: #fff; /* Initial background color with alpha (transparency) */
            transition: background-color 0.5s ease; /* Transition effect for background-color property */
        }

        .clear {
            clear: both;
        }

        .container:hover {
            background-color: rgba(255, 255, 255, 0.5); /* Background color with less alpha on hover */
        }
        
        .orderscontainer:hover {
            background-color: rgba(255, 255, 255, 0.5); /* Background color with less alpha on hover */
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            margin-bottom: 10px;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        select, input {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            display: inline-block;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .delete-button {
            background-color: #704241;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        .delete-button:hover {
            background-color: #c0392b;
        }

        #orderSearch {
            margin-bottom: 10px;
        }
        footer {
            background color:
            padding: 
            text-align: center;
        }
        .tagline {
            font-size: 25px;
            font-family: 'brush script mt';
        }
        .credits-footer {
            font-size: 12px;
            font-family: 'italic', sans-serif;
        }

        .developer-name {
            font-weight: bold;
        }
         .status {
            font-weight: bold;
            padding: 5px;
        }
        .blinking {
        animation: blink 1s infinite;
    }

    @keyframes blink {
        5% {
            opacity: 30%;
        }
    }

        .paid {
            color: green;
        }

        .pending {
            color: orange;
        }
        /* Add this style for the welcome message */
.welcome-message {
    font-size: 18px;
    font-weight: bold;
    color: 	black; /* Green color, adjust as needed */
    margin-bottom: 20px;
    display: flex;
    align-items: center;
}

/* Add this style for the username */
.logged-in-username {
    margin-left: 10px;
}
/* Calculator icon styles */
.calculator-icon {
    position: fixed;
    top: 10px;
    left: 10px;
    cursor: pointer;
    z-index: 1001;
}

.calculator-icon img {
    width: 40px; /* Set the width and height according to your icon size */
}

/* Calculator container styles */
.calculator-container {
    position: fixed;
    top: 50%;
    left: -300px; /* Initially position it off-screen on the left side */
    transform: translateY(-50%);
    width: 300px;
    background-color: #fff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    transition: left 0.3s ease-in-out;
}

.calculator-content {
    padding: 20px;
}

.calculator-toggle {
    text-align: center;
    padding: 10px;
    background-color: #3498db;
    color: #fff;
    cursor: pointer;
}


    </style>
</head>
<!--------------------------------------------------------------------------HTML--------------------------------------------------------------------->
<body>

<div class="wrapper">
        <div class="container">
            <h1>Coffee Buddies Cafe</h1>
            <div class="tagline">
            <p>"Brewed Bliss & Cookie Bits!"</p>
</div>

            <div class="welcome-message">
    Our Barista for Today,  <?php echo $username; ?>!
    <span class="logged-in-username"></span>
</div>  

    <!-- Order Form -->
    <form action="order_form.php" method="post" id="orderForm">
        <label for="customer_name">Customer Name:</label>
        <input type="text" name="customer_name" id="customer_name" required>

        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" id="quantity" value="1" min="1" onchange="updatePrice()" required>

        <label for="cookie_type">Cookie Type:</label>
        <select name="cookie_type" id="cookie_type" onchange="updatePrice()">
            <option value="original" data-price="60">Original</option>
            <option value="coffee" data-price="60">Coffee</option>
            <option value="oatmeal" data-price="70">Oatmeal</option>
            <option value="red_velvet" data-price="75">Red Velvet</option>
            <option value="HOT_Americano" data-price="50">HOT: Coffee Americano</option>
            <option value="HOT_Latte" data-price="50">HOT: Coffee Latte</option>
            <option value="ICED_Americano" data-price="60">ICED: Coffee Americano</option>
            <option value="ICED_Latte" data-price="60">ICED: Coffee Latte</option>
            <option value="ICED_CoffeeVanilla" data-price="70">ICED: Coffee with Vanilla Syrup</option>
            <option value="ICED_CoffeeCaramel" data-price="70">ICED: Coffee with Caramel Syrup</option>
            <option value="ICED_CoffeeHazelnut" data-price="70">ICED: Coffee with Hazelnut Syrup</option>
        </select>
        <label for="price">Total Price:</label>
        <input type="text" name="price" id="price" value="60" readonly>

        <button type="submit" name="addOrder" id="addOrderButton" onclick="addOrderAndReload()" >Add Order</button>
     
        
<div class="credits-footer">
        <p>Developed by <span class="developer-name">Marlon Dela Cruz</span></p>
        <p>Contact Us: <span class="developer-name"> +63995 385 6860</span></p>
            <p>Email: <span class="developer-name"> marlon.dcrodriguez@gmail.com</span></p>
            <p>&copy; 2024 Brewed Bliss & Cookie Bits Web App</p>
            <!------------------------------------------------------CALCULATOR--------------------------------------------------------->
            
            <div id="calculator-icon" class="calculator-icon" onclick="openWindowsCalculator()">
        <img src="calculator-icon.png" alt="Calculator Icon">
    </div>


    <!-- Calculator container -->
    <div id="calculator-container" class="calculator-container">
        <div id="calculator-content" class="calculator-content">
            <!-- Calculator content goes here -->
            <input type="text" id="calc-display" readonly>
            <!-- ... other calculator buttons and display -->
        </div>
        <div id="calculator-toggle" class="calculator-toggle" onclick="toggleCalculator()">Minimize</div>
    </div>

    <!------------------------------------------------------CALCULATOR--------------------------------------------------------->
    </div>

</form>
    </div>
    <div class="wrapper">
    <div class="orderscontainer">
<h2>Search Order:</h2>
<input type="text" id="orderSearch" placeholder="Search by Order ID" oninput="searchOrders()">
    <h3>Orders</h3>
    <table>
        <thead>
        <tr>
            <th>Order ID</th>
            <th>Customer Name</th>
            <th>Cookie Type</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Order Date</th>
            <th>Actions</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cookiestore";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["customer_name"]) && isset($_POST["quantity"]) && isset($_POST["cookie_type"]) && isset($_POST["price"])) {
        $customerName = $_POST["customer_name"];
        $quantity = $_POST["quantity"];
        $cookieType = $_POST["cookie_type"];
        $price = $_POST["price"];

        $sql = "INSERT INTO orders (customer_name, quantity, cookie_type, price) VALUES ('$customerName', $quantity, '$cookieType', $price)";

        if ($conn->query($sql) === TRUE) {
            // Instead of echo, redirect after successful insertion
            header("Location: {$_SERVER['REQUEST_URI']}", true, 303);   
            exit();
        } else {
            echo "Error inserting record: " . $conn->error;
        }
    } elseif (isset($_POST['action']) && isset($_POST['order_id']) && $_POST['action'] === 'delete') {
        $order_id = $_POST['order_id'];
        $sql = "DELETE FROM orders WHERE order_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $order_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Order deleted successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error deleting order: " . $stmt->error]);
        }

        $stmt->close();
    }
}
$sql = "SELECT * FROM orders";
$result = $conn->query($sql);

if ($result !== false && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr id='orderRow_" . $row["order_id"] . "'>";
        echo "<td>" . $row["order_id"] . "</td>";
        echo "<td>" . $row["customer_name"] . "</td>";
        echo "<td class='flavor-editable'>" . $row["cookie_type"] . "</td>";
        echo "<td class='quantity-editable'>" . $row["quantity"] . "</td>";
        echo "<td class='price-editable'>" . $row["price"] . "</td>";
        echo "<td>" . $row["order_date"] . "</td>";
        echo "<td class='action-buttons'>";
        echo "<button class='delete-button' onclick='manageOrder(" . $row["order_id"] . ", \"delete\")'>Delete</button>";
        echo "</td>";
        echo "<td class='status'>";

        // Add the blinking class for "Unpaid" orders
        $statusClass = strtolower($row["status"]);
        if ($statusClass === 'unpaid') {
            $statusClass .= ' blinking';
        }

        echo "<button class='status-button $statusClass' onclick='changeStatus(" . $row["order_id"] . ", \"" . $row["status"] . "\")'>" . $row["status"] . "</button>";
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7'>No orders found</td></tr>";
}

$conn->close();
?>


        </tbody>
    </table>   
    <button id="exportExcelButton" onclick="exportToExcel()">Export to Excel</button>
   
    <script>

function changeStatus(orderId, currentStatus) {
    var statusButton = document.querySelector("#orderRow_" + orderId + " .status-button");

    // Determine the new status based on the current status
    var newStatus = currentStatus === 'Paid' ? 'Pending' : 'Paid';

    // AJAX request to update the status in the database
    $.ajax({
        type: 'POST',
        url: 'manage_order.php',
        data: {
            action: 'changeStatus',
            order_id: orderId,
            new_status: newStatus
        },
        dataType: 'json',
        success: function (response) {
            // Handle the response
            console.log(response);
            if (response.status === 'success') {
                // Update the button appearance based on the new status
                updateButtonAppearance(statusButton, response.new_status);
            } else {
                // Handle errors if needed
                console.error(response.message);
            }
        },
        error: function (error) {
            console.error(error);
            // Handle errors if needed
        }
    });
}




// ===========================================================================================UNPAID BUTTON============================================
document.addEventListener('DOMContentLoaded', function () {
    // Simulate the login status (you should replace this with your actual logic)
    var isLoggedIn = true; // Set to false if not logged in
  
    var deleteButtons = document.querySelectorAll('.delete-button');
    deleteButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            var orderId = button.id.split('_')[1];

            // Show confirmation prompt
            var isConfirmed = confirm("Are you sure you want to delete this order?");

            if (isConfirmed) {
                // Call the manageOrder function with 'delete' action
                manageOrder(orderId, 'delete');
            }
        });
    });
})
    // Select all status buttons
    var statusButtons = document.querySelectorAll('.status-button');
    statusButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            var currentStatus = button.textContent.trim();

    // Set initial appearance based on login status
    statusButtons.forEach(function (button) {
        updateButtonAppearance(button, isLoggedIn ? 'Pending' : 'Unpaid');
    });
})
    })
    // Fetch order statuses from the server and update buttons accordingly
    function fetchOrderStatuses() {
    var statusButtons = document.querySelectorAll('.status-button');

    statusButtons.forEach(function (button) {
        var orderId = button.id.split('_')[1];

        fetch('get_order_details.php?orderId=' + orderId)
            .then(response => response.json())
            .then(orderDetails => {
                if (orderDetails && orderDetails.status) {
                    updateButtonAppearance(button, orderDetails.status);
                }
            })
            .catch(error => {
                console.error('Error fetching order details:', error);
            });
    });
}

function updateButtonAppearance(button, newStatus) {
    // Update the button text and class based on the new status
    button.innerHTML = newStatus;
    button.classList.remove('blinking', 'pending', 'paid'); // Remove all classes
    button.classList.add(newStatus.toLowerCase());

    // Add or remove blinking class based on the new status
    if (newStatus === 'Pending') {
        button.classList.add('blinking', 'pending');
    }
}


function fetchOrderStatuses() {
    // Select all status buttons
    var statusButtons = document.querySelectorAll('.status-button');

    // Loop through each status button
    statusButtons.forEach(function (button) {
        // Extract the order ID from the button's ID
        var orderId = button.id.split('_')[1];

        // Fetch order details from the server using get_order_details.php
        $.ajax({
            type: 'GET',
            url: 'get_order_details.php',
            data: { orderId: orderId },
            dataType: 'json',
            success: function (orderDetails) {
                // Update the button appearance based on the actual status from the database
                if (orderDetails && orderDetails.status) {
                    updateButtonAppearance(button, orderDetails.status);
                }
            },
            error: function (error) {
                console.error('Error fetching order details:', error);
            }
        });
    });
}

// Fetch order statuses when the document is ready
$(document).ready(function () {
    fetchOrderStatuses();
});

// ===========================================================================================UNPAID BUTTON============================================

       function enableInlineEdit(orderId) {
    var row = document.getElementById('orderRow_' + orderId);

    // Disable other edit buttons
    var editButtons = document.querySelectorAll('.edit-button');
    editButtons.forEach(function (button) {
        button.disabled = true;
    });

    // Hide delete buttons while editing
    var deleteButtons = document.querySelectorAll('.delete-button');
    deleteButtons.forEach(function (button) {
        button.style.display = 'none';
    });

    // Change the Edit button to Save
    var editButton = row.querySelector('.edit-button');
    editButton.textContent = 'Save';
    editButton.onclick = function () {
        saveChanges(orderId);
    };

    // Enable input fields for editing
    var inputs = row.querySelectorAll('td:not(:last-child) input');
    inputs.forEach(function (input) {
        input.disabled = false;
    });

    // Enable flavor dropdown
    var flavorCell = row.querySelector('.flavor-editable');
    var currentFlavor = flavorCell.innerText;
    flavorCell.innerHTML = "<select id='flavor_" + orderId + "'></select>";

    var flavorDropdown = row.querySelector('#flavor_' + orderId);

    // Add flavor options
    var flavorOptions = ['Original', 'Coffee', 'Oatmeal', 'Red Velvet'];
    for (var i = 0; i < flavorOptions.length; i++) {
        var option = document.createElement('option');
        option.value = flavorOptions[i].toLowerCase();
        option.text = flavorOptions[i];
        flavorDropdown.add(option);
    }

    flavorDropdown.value = currentFlavor.toLowerCase();

    // Enable quantity input
    var quantityCell = row.querySelector('.quantity-editable');
    var currentQuantity = quantityCell.textContent;
    quantityCell.innerHTML = `<input type="number" id="editQuantity_${orderId}" value="${currentQuantity}" min="1" required>`;

   // Enable price input
var priceCell = row.querySelector('.price-editable');
var currentPrice = priceCell.textContent;
priceCell.innerHTML = `<input type="text" id="editPrice_${orderId}" value="${currentPrice}" readonly>`;
}


function addOrderAndReload() {
    // Disable the button to prevent multiple submissions
    $('#addOrderButton').prop('disabled', true);

    // Extract form data
    var customerName = $('#customer_name').val();

    // Check if customer name is provided
    if (!customerName.trim()) {
        // Display a notification that customer name is required
        alert("Please provide the customer name.");

        // Re-enable the button
        $('#addOrderButton').prop('disabled', false);
        return; // Do not proceed with the form submission
    }

    var formData = $('#orderForm').serialize();

    // AJAX request to add an order
    $.ajax({
        type: 'POST',
        url: 'add_order.php',
        data: formData + '&action=add', // Include the action parameter
        dataType: 'json',
        success: function (response) {
            console.log(response);
            // You can handle the response here, for example, display a success message or update the order table
            // Reload the current page to clear the form for the next order
            location.reload();
        },
        error: function (error) {
            console.error(error);
            // Handle errors if needed
        }
    });
}



// After successful form submission
header("Location: order_form.php");
exit();

function updatePrice() {
    var quantity = document.getElementById('quantity').value;
    var cookieType = document.getElementById('cookie_type');
    var price = document.getElementById('price');

    var selectedCookieType = cookieType.options[cookieType.selectedIndex];
    var unitPrice = selectedCookieType.getAttribute('data-price');

    price.value = quantity * unitPrice;
}




        function cancelEdit() {
            document.getElementById('editOrderSection').style.display = 'none';
            document.getElementById('addOrderButton').style.display = 'inline-block';
            document.getElementById('orderForm').reset();
        }

        function editOrder(orderId) {
            // Display the edit form
            document.getElementById('editOrderSection').style.display = 'block';
            // Hide the "Add Order" button
            document.getElementById('addOrderButton').style.display = 'none';

            // Populate the edit form fields with existing data
            document.getElementById('editOrderId').value = orderId;
        }

        function manageOrder(orderId, action) {
            // Define the data to be sent in the AJAX request
            var data = {
                action: action,
                order_id: orderId
            };

            // Define the URL for the AJAX request
            var url = 'manage_order.php';

            // Make the AJAX request
            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                dataType: 'json',
                success: function (response) {
                    // Handle the response from the server
                    if (response.status === 'success') {
                        // Perform any necessary actions on success
                        console.log(response.message);
                        if (action === 'delete') {
                            location.reload(); // Reload the page only on delete
                        }
                    } else {
                        // Handle errors or display messages
                        console.error(response.message);
                    }
                }
            });
        }
        function searchOrders() {
    var input, filter, table, tr, tdOrderID, tdCustomerName, i, txtValue;
    input = document.getElementById("orderSearch");
    filter = input.value.toUpperCase();
    table = document.querySelector("table");
    tr = table.getElementsByTagName("tr");

    // Loop through all table rows, and hide those that don't match the search query
    for (i = 0; i < tr.length; i++) {
        tdOrderID = tr[i].getElementsByTagName("td")[0]; // Assuming Order ID is in the first column
        tdCustomerName = tr[i].getElementsByTagName("td")[1]; // Assuming Customer Name is in the second column

        if (tdOrderID && tdCustomerName) {
            var orderIDText = tdOrderID.textContent || tdOrderID.innerText;
            var customerNameText = tdCustomerName.textContent || tdCustomerName.innerText;

            // Check if either order ID or customer name contains the filter text
            if (orderIDText.toUpperCase().indexOf(filter) > -1 || customerNameText.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}



    function exportToExcel() {
    // Get all rows from the table
    var table = document.querySelector("table");
    var rows = table.querySelectorAll("tbody tr");

    // Create a new Excel workbook
    var wb = XLSX.utils.book_new();

    // Add a worksheet to the workbook
    var ws = XLSX.utils.table_to_sheet(table);

    // Add the worksheet to the workbook
    XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

    // Save the workbook as an Excel file
    XLSX.writeFile(wb, "exported_data.xlsx");

    console.log("Export successful!");
}

    
    </script>

<!-- Add this button at the end of your order_form.php file -->
<button onclick="closeStore()">Close Store</button>

<script>
function confirmCloseStore() {
    // Display a confirmation dialog
    var userConfirmation = confirm("Are you sure you want to close the store? This will export data and clear orders.");

    // Check the user's response
    if (userConfirmation) {
        // User clicked "OK" or "Yes," proceed with the closeStore action
        closeStore();
    } else {
        // User clicked "Cancel" or "No," do nothing or provide feedback
        alert("Store closure canceled by user.");
    }
}

function closeStore() {
    // Ask for confirmation
    var confirmClose = confirm("Are you sure you want to close the store?");
    
    // If user confirms, proceed with the actions
    if (confirmClose) {
        // Get data for export
        var table = document.querySelector("table");
        var rows = table.querySelectorAll("tbody tr");

        // Create a new Excel workbook
        var wb = XLSX.utils.book_new();

        // Add a worksheet to the workbook
        var ws = XLSX.utils.table_to_sheet(table);

        // Add the worksheet to the workbook
        XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

        // Save the workbook as an Excel file
        var today = new Date();
        var dateString = today.toISOString().slice(0, 10); // Format: YYYY-MM-DD
        XLSX.writeFile(wb, "exported_data_" + dateString + ".xlsx");

        // Call close_store.php to clear orders data from the database
        // Make sure the path is correct, adjust it as needed
        var closeStoreUrl = 'close_store.php';
        var xhr = new XMLHttpRequest();
        xhr.open('GET', closeStoreUrl, true);
        xhr.onload = function () {
            if (xhr.status == 200) {
                console.log(xhr.responseText);
                // Redirect to the login page
                window.location.href = 'login.php';
            } else {
                console.error('Error while closing store:', xhr.statusText);
            }
        };
        xhr.send();

        console.log("Store closed successfully!");
    } else {
        // If user cancels, do nothing
        console.log("Store closure canceled by user.");
    }
}
function openWindowsCalculator() {
            // Open Windows Calculator using a custom protocol
            window.location.href = "calculator://";
        }

        function toggleCalculator() {
            var calculatorContainer = document.getElementById("calculator-container");
            var calculatorToggle = document.getElementById("calculator-toggle");

            if (calculatorContainer.style.display === "none" || calculatorContainer.style.display === "") {
                calculatorContainer.style.display = "block";
                calculatorToggle.innerText = "Minimize";
            } else {
                calculatorContainer.style.display = "none";
                calculatorToggle.innerText = "Open Calculator";
            }
        }
</script>
<!-- Add this container at the end of your body -->
<div id="logout-container">
    <form method="post" action="">
        <input type="submit" name="logout" value="Logout">
    </form>
</div>
    
</div>
</body>
</html>

