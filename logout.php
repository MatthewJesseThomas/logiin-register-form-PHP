<?php
require 'config.php';

if (isset($_POST['submit'])) {
    $fullnameemail = $_POST['fullnameemail'];
    $password = $_POST['password'];
    $result = mysqli_query($conn, "SELECT * FROM employee WHERE fullname = '$fullnameemail' OR email = '$fullnameemail' OR password = '$password'");
    $row = mysqli_fetch_assoc($result);

    if (mysqli_num_rows($result) > 0) {
        if ($password == $row['password']) {
            $_SESSION["login.php"] = true;
            $_SESSION["id"] = $row['user_id'];
            header("Location: logout_index.php");
        } else {
            echo "
            <script> 
            alert('Incorrect Password');
            </script>";
        }
    } else {
        echo "
        <script> 
        alert('User Not Registered');
        </script>";
    }
} elseif (isset($_POST['logout'])) { // Check if the logout form is submitted
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session
    echo "
    <script> 
    alert('Successfully Logged Out');
    </script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <title>Login</title>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <h2 class="mt-3 text-center">Logout</h2>
                <br>
                <div class="log-out d-flex align-items-center justify-content-center">
                <form method="post">
                    <input type="submit" name="logout" value="Logout">
                </form>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz"
    crossorigin="anonymous"></script>

</html>