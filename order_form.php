<?php
ob_start(); 
session_start();
if (!isset($_SESSION['username'])) {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit();
}
include 'db_connect.php';
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
    <title>Brewed Bliss & Cookie Bits</title>
    <style>
            
        
        body {
            font-family: Arial, sans-serif;
            background: url('your-background-image.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            font-family: 'Mastery Kingdom', sans-serif;
            overflow-x: auto;
            background-color: #fff; /* Initial background color with alpha (transparency) */
            transition: background-color 0.5s ease; /* Transition effect for background-color property */
        }

        .container:hover {
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
            background-color: #e74c3c;
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

    </style>
</head>
<!--------------------------------------------------------------------------HTML--------------------------------------------------------------------->
<body>
<div class="container">
    <h1>Brewed Bliss & Cookie Bits</h1>


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
        </select>
        <label for="price">Total Price:</label>
        <input type="text" name="price" id="price" value="60" readonly>

        <button type="submit" name="addOrder" id="addOrderButton" onclick="addOrderAndReload()" >Add Order</button>



       

        

</form>
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
        echo "<td class='quantity-editable'>" . $row["quantity"] . "</td>"; // Make sure to display quantity in the table
        echo "<td class='price-editable'>" . $row["price"] . "</td>";
        echo "<td>" . $row["order_date"] . "</td>";
        echo "<td class='action-buttons'>";
        echo "<button class='delete-button' onclick='manageOrder(" . $row["order_id"] . ", \"delete\")'>Delete</button>";
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


</script>
<form method="post" action="">
        <input type="submit" name="logout" value="Logout">
    </form>


    
</div>
</body>
</html>

