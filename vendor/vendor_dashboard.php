<?php
session_start();
if (!isset($_SESSION["vendor_id"]) || $_SESSION["role"] !== "vendor") {
    header("Location: login.html");
    exit;
}
require '../db.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Dashboard</title>
    <link rel="stylesheet" href="../assets/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="dark-mode.js"></script>
    



    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            overflow-y: hidden;
        }

        .dashboard-container {
            max-height: 90vh;
            max-width: 1100px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow-y: auto;
        }

        h2, h3 {
            text-align: center;
            color: #333;
            margin-bottom: 15px;
        }

        /* Layout: Orders Left | Menu Right */
        .dashboard-grid {
            display: flex;
            gap: 20px;
        }

        .orders-section, .menu-section {
            max-height: none;
            flex: 1;
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            overflow: visible;
        }

        /* Orders Section */
        #orders-container {
            max-height: 90vh;
            overflow-y: scroll;
            overflow-x: scroll;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            background: #fff;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        

        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        /* Buttons */
        button {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn {
    display: block;
    text-align: center;
    width: 150px; /* Adjust width if necessary */
    margin: 20px auto; /* Center horizontally */
    padding: 10px 15px;
    background: #dc3545; /* Red color for logout */
    color: white;
    text-decoration: none;
    font-weight: bold;
    border-radius: 5px;
}

        .update-order {
            background-color: #28a745;
            color: white;
        }

        .delete-item {
            background-color: #dc3545;
            color: white;
        }

        /* Order Notification */
        #order-notification {
            display: none;
            background: red;
            color: white;
            padding: 5px 10px;
            border-radius: 50%;
            position: relative;
            top: -5px;
            left: 5px;
        }

        /* Manage Menu Section */
        .manage-menu {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        input, button {
            padding: 10px;
            width: 100%;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            background: #007bff;
            color: white;
            font-weight: bold;
        }

        /* Ensure page loads at the top */
        html, body {
            scroll-behavior: smooth;
            overflow-x: hidden;
        }
        /* Toggle Switch */
.switch {
    position: relative;
    display: inline-block;
    width: 40px;
    height: 20px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: 0.4s;
    border-radius: 20px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 14px;
    width: 14px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #28a745;
}

input:checked + .slider:before {
    transform: translateX(20px);
}


        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-grid {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
    
        <h2 id="top">Welcome, <?php echo $_SESSION["first_name"]; ?>!</h2>
        

        <div class="dashboard-grid">
    
            <!-- Orders Section -->
            <div class="orders-section">
                <h3>Orders <span id="order-notification">0</span></h3>
                <div id="orders-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Items</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="orders-list">
                            <!-- Orders will be loaded here via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Menu Section -->
            <div class="menu-section">
                <h3>Manage Menu</h3>
                <form id="add-item-form" enctype="multipart/form-data" class="manage-menu" action="add_item.php" method="POST">

                    <input type="text" id="item_name" name="item_name" placeholder="Item Name" required>
                    <input type="number" id="price" name="price" placeholder="Price (₱)" required>
                    <input type="file" id="item_image" name="item_image" accept="image/*" required>
                    <button type="submit">Add Item</button>
                </form>

                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Item Name</th>
                            <th>Price (₱)</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="menu-list">
                        <!-- Menu items will be loaded here via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
        <!-- <button id="darkModeToggle" class="dark-mode-btn">
    🌙
</button> -->

        <a href="logout.php" class="btn">Logout</a>
    </div>

    <script>
       $(document).ready(function () {
    function loadOrders() {
        $.get("orders.php", function (data) {
            $("#orders-list").html(data);
        });
    }

    // Load orders on page load
    loadOrders();

    // Refresh orders every 5 seconds
    setInterval(loadOrders, 5000);

    function loadMenu() {
        $.get("menu.php", function (data) {
            $("#menu-list").html(data);
        });
    }

    loadMenu();

    $(document).on("change", ".toggle-status", function () {
        var itemId = $(this).data("id");
        var newStatus = $(this).prop("checked") ? "Available" : "Out Of Stock";

        $.post("update_status.php", { item_id: itemId, status: newStatus }, function (response) {
            if (response.trim() !== "success") {
                alert("Failed to update status.");
                loadMenu(); // Reload if error
            }
        });
    });

    $(document).on("click", ".update-order", function () {
        var orderId = $(this).data("id");

        $.post("update_order.php", { order_id: orderId }, function (response) {
            if (response.trim() === "success") {
                loadOrders();
            } else {
                alert("Failed to update order status.");
            }
        });

    });


    // Ensure sound plays after user interaction

    let notificationSound = new Audio('../assets/notification.mp3');

function checkNewOrders() {
    $.get("check_new_orders.php", function (data) {
        if (data > 0) {
            $("#order-notification").text(data).show();
            
            // Play sound only if notification is updated
            if ($("#order-notification").text() !== data.toString()) {
                notificationSound.play().catch(error => console.log("Autoplay blocked:", error));
            }
        } else {
            $("#order-notification").hide();
        }
    });
}

// Run checkNewOrders every 5 seconds
setInterval(checkNewOrders, 5000);

    $(document).on("click", ".delete-item", function () {
        var itemId = $(this).data("id");

        if (confirm("Are you sure you want to delete this item?")) {
            $.post("delete_item.php", { item_id: itemId }, function (response) {
                if (response.trim() === "success") {
                    alert("Item deleted successfully.");
                    loadMenu(); // Refresh the menu
                } else {
                    alert("Failed to delete item: " + response);
                }
            });
        }
    });

    // Check new orders every 5 seconds
    setInterval(checkNewOrders, 5000);

    // Ensure page always starts at the top on load
    $(window).on("load", function () {
        $(document).scrollTop(0);
    });
});
    </script>
</body>
</html>