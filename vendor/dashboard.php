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
        <script src="dark-mode.js"></script>
        


    </head>
    <body>
        <div class="dashboard-container">
            <h2>Welcome, <?php echo $_SESSION["first_name"]; ?>!</h2>
            
            <h3>Orders</h3>
            <table border="1">
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

            <h3>Manage Menu</h3>
            <form id="add-item-form">
                <input type="text" id="item_name" name="item_name" placeholder="Item Name" required>
                <input type="number" id="price" name="price" placeholder="Price" required>
                <button type="submit">Add Item</button>
            </form>
            
            <table border="1">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="menu-list">
                    <!-- Menu items will be loaded here via AJAX -->
                </tbody>
            </table>

            <a href="logout.php" class="btn">Logout</a>
        </div>

        <script>
            $(document).ready(function () {
                // Load orders
                function loadOrders() {
                    $.get("orders.php", function (data) {
                        $("#orders-list").html(data);
                    });
                }
                loadOrders();

                // Update order status
                $(document).on("click", ".update-order", function () {
                    var orderId = $(this).data("id");
                    $.post("update_order.php", { order_id: orderId }, function () {
                        loadOrders();
                    });
                });

                // Load menu
                function loadMenu() {
                    $.get("menu.php", function (data) {
                        $("#menu-list").html(data);
                    });
                }
                loadMenu();

                // Add menu item
                $("#add-item-form").submit(function (e) {
                    e.preventDefault();
                    var itemName = $("#item_name").val();
                    var price = $("#price").val();

                    $.post("add_item.php", { item_name: itemName, price: price }, function () {
                        loadMenu();
                        $("#add-item-form")[0].reset();
                    });
                });

                // Delete menu item
                $(document).on("click", ".delete-item", function () {
                    var itemId = $(this).data("id");
                    $.post("delete_item.php", { item_id: itemId }, function () {
                        loadMenu();
                    });
                });
            });
        </script>
    </body>
    </html>
