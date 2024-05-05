<?php
session_start();
// Check if user is already logged in
if(isset($_SESSION['user'])) {
    header('location: dashboard.php');
    exit; // Make sure to exit after redirection
}

$error_message = '';
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include the connection file
    include('connection.php');

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to check user credentials
    $query = 'SELECT * FROM user WHERE user.email = :username AND user.password = :password LIMIT 1';
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->execute();

    if($stmt->rowCount() > 0) {
        // Fetch user details
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['user'] = $user; // Set user data in session
        // Redirect to the dashboard
        header('Location: dashboard.php');
        exit; // Make sure to exit after redirection
    } else {
        $error_message = 'Enter correct username and password.';
        // No need to exit here to allow the code to continue and display the login page
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Stock Management</title>
    <link rel="stylesheet" type="text/css" href="login1.css">  
    <style>
        /* Add your custom CSS styles here */
        #errorMessage {
            position: relative;
            margin-top: 10px; /* Adjust as needed */
            color: red;
        }
    </style>
</head>
<body id="loginBody">
    <form action="" method="POST">
        <div class="login">
            <h3> Stock Management System</h3>
            <h1><ul><u>SMS</u></ul></h1> 
            <div class="LoginHeader">
                <div class="username">
                    <label for=""> Username:</label>
                    <input type="text" name="username" placeholder="Username" required=""><br><br>

                    <label for="">Password:</label>
                    <input type="password" name="password" placeholder="Password" required=""><br><br>
                    
                    <button type="submit">Login</button>
                </div>
            </div>
        </div>
        <?php if(!empty($error_message)) { ?> 
        <div id="errorMessage">
            <strong>ERROR:</strong>
            <p><?= $error_message ?></p>
        </div>
        <?php } ?>
    </form>
</body>
</html>
