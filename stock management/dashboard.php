<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('location: login.php');
    exit; 
}

$user = $_SESSION['user'];

include 'connection.php';

if (isset($conn)) {
    try {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["create_stock"])) {
            $item_name = $_POST["item_name"];
            $item_quantity = $_POST["item_quantity"];
            $item_price = $_POST["item_price"];

            $sql = "INSERT INTO stock_items (name, quantity, price) VALUES (:item_name, :item_quantity, :item_price)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':item_name', $item_name);
            $stmt->bindParam(':item_quantity', $item_quantity);
            $stmt->bindParam(':item_price', $item_price);
            $stmt->execute();
            $success_message = "New item added to stock successfully";
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_stock"])) {
            $item_id = $_POST["item_id"];

            $sql = "DELETE FROM stock_items WHERE id=:item_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':item_id', $item_id);
            $stmt->execute();
            $success_message = "Item deleted successfully";
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_stock"])) {
            $item_id = $_POST["item_id"];
            $item_name = $_POST["item_name"];
            $item_quantity = $_POST["item_quantity"];
            $item_price = $_POST["item_price"];

            $sql = "UPDATE stock_items SET name=:item_name, quantity=:item_quantity, price=:item_price WHERE id=:item_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':item_name', $item_name);
            $stmt->bindParam(':item_quantity', $item_quantity);
            $stmt->bindParam(':item_price', $item_price);
            $stmt->bindParam(':item_id', $item_id);
            $stmt->execute();
            $success_message = "Item updated successfully";
        }

        $sql = "SELECT * FROM stock_items";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Stock Management System</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <style>
        /* Add your custom CSS styles here */
        .hidden {
            display: none;
        }

        #successMessage {
            margin-top: 10px;
            color: green;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<div id="dashboardMainContainer">
    <div class="dashboard_sidebar">
        <h3 class="dashboard_logo">Stock Management System</h3>
        <div class="dashboard_sidebar_user">
            <image src="images/logo.png" alt="user image." />
            <span><?= $user['first_name'].' '.$user['last_name'] ?></span>
        </div>
        <div class="dashboard_sidebar_menus">
            <ul class="dashboard_menu_lists">
                <li><a href="#" class="menuActive" id="viewProducts"><i class="fa fa-dashboard"></i>View Products</a></li>
                <li><a href="#" id="addProducts"><i class="fa fa-dashboard"></i>Add Products</a></li>
                <li><a href="#" id="updateProducts"><i class="fa fa-dashboard"></i>Update Products</a></li>
                <li><a href="#" id="deleteProducts"><i class="fa fa-dashboard"></i>Delete Products</a></li>
            </ul>
        </div>
    </div>
    <div class="dashboard_content_container">
        <div class="dashboard_topNav">
            <a href=""><i class="fa fa-navicon"></i></a>
            <a href="logout.php" id="logoutbtn"><i class="fa fa-power-off"></i>Log-out</a>
        </div>
        <div class="dashboard_content">
            <div class="dashboard_content_main" id="productTable">
                <!-- Product Table will be displayed here -->
                <?php if (!empty($result)) { ?>
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                        </tr>
                        <?php foreach ($result as $row) { ?>
                            <tr>
                                <td><?= $row["id"] ?></td>
                                <td><?= $row["name"] ?></td>
                                <td><?= $row["quantity"] ?></td>
                                <td>$<?= $row["price"] ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                <?php } else { ?>
                    <div>0 results</div>
                <?php } ?>
            </div>
            <div class="dashboard_content_main hidden" id="addProductForm">
                <!-- HTML Form for Add (Insert) operation -->
                <form method="post">
                    <input type="text" name="item_name" placeholder="Name">
                    <input type="number" name="item_quantity" placeholder="Quantity">
                    <input type="number" name="item_price" placeholder="Price">
                    <button type="submit" name="create_stock">Add Product</button>
                </form>
            </div>
            <div class="dashboard_content_main hidden" id="updateProductForm">
                <!-- HTML Form for Update operation -->
                <form method="post">
                    <input type="number" name="item_id" placeholder="ID">
                    <input type="text" name="item_name" placeholder="New Name">
                    <input type="number" name="item_quantity" placeholder="New Quantity">
                    <input type="number" name="item_price" placeholder="New Price">
                    <button type="submit" name="update_stock">Update Product</button>
                </form>
                <?php if(isset($success_message)) { ?>
                    <div id="successMessage"><?= $success_message ?></div>
                <?php } ?>
            </div>
            <div class="dashboard_content_main hidden" id="deleteProductForm">
                <!-- HTML Form for Delete operation -->
                <form method="post">
                    <input type="number" name="item_id" placeholder="ID">
                    <button type="submit" name="delete_stock">Delete Product</button>
                </form>
                <?php if(isset($success_message)) { ?>
                    <div id="successMessage"><?= $success_message ?></div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<script>
    const productTable = document.getElementById('productTable');
    const addProductForm = document.getElementById('addProductForm');
    const updateProductForm = document.getElementById('updateProductForm');
    const deleteProductForm = document.getElementById('deleteProductForm');

    const viewProducts = document.getElementById('viewProducts');
    const addProducts = document.getElementById('addProducts');
    const updateProducts = document.getElementById('updateProducts');
    const deleteProducts = document.getElementById('deleteProducts');

    viewProducts.addEventListener('click', () => {
        productTable.classList.remove('hidden');
        addProductForm.classList.add('hidden');
        updateProductForm.classList.add('hidden');
        deleteProductForm.classList.add('hidden');
    });

    addProducts.addEventListener('click', () => {
        productTable.classList.add('hidden');
        addProductForm.classList.remove('hidden');
        updateProductForm.classList.add('hidden');
        deleteProductForm.classList.add('hidden');
    });

    updateProducts.addEventListener('click', () => {
        productTable.classList.add('hidden');
        addProductForm.classList.add('hidden');
        updateProductForm.classList.remove('hidden');
        deleteProductForm.classList.add('hidden');
    });

    deleteProducts.addEventListener('click', () => {
        productTable.classList.add('hidden');
        addProductForm.classList.add('hidden');
        updateProductForm.classList.add('hidden');
        deleteProductForm.classList.remove('hidden');
    });
</script>

</body>
</html>
