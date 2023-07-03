<?php
require 'config.php';

function logInEmployee($employeeId, $employeeName) {
    // Store the logged-in employee's information in a log file or database
    // In this example, we will simply log the message to the PHP error log
    error_log("Employee with ID $employeeId and name $employeeName logged in.");
}

if (isset($_POST['submit'])) {
    if (isset($_POST['fullnameemail']) && isset($_POST['password'])) {
        $fullnameemail = $_POST['fullnameemail'];
        $password = $_POST['password'];
        $result = mysqli_query($conn, "SELECT * FROM employee WHERE fullname = '$fullnameemail' OR email = '$fullnameemail'");
        $row = mysqli_fetch_assoc($result);

        if (mysqli_num_rows($result) > 0) {
            // Check if the provided password matches either the hashed password or the plaintext password
            if (password_verify($password, $row['password']) || $password === $row['password']) {
                // Password is correct, log in the user
                $_SESSION["login"] = true;
                $_SESSION["id"] = $row['employee_id'];
                $_SESSION["name"] = $row['fullname'];

                logInEmployee($row['employee_id'], $row['fullname']); // Call the function to log the message

                header("Location: https://dev-clockops.pantheonsite.io/");
                exit();
            } else {
                echo '
         <script>
            Swal.fire({
            title: "Oops",
            text: "Incorrect Password!",
            icon: "error",
            confirmButtonText: "OK"
    });
    console.log("Incorrect Password");
        </script>';
            }
        } else {
            echo '
         <script>
            Swal.fire({
            title: "Oops",
            text: "User Not Registered!!!",
            icon: "error",
            confirmButtonText: "OK"
    });
    console.log("User Not Registered");
        </script>';
        }
    } else {
        echo '
         <script>
            Swal.fire({
            title: "Oops",
            text: "Invalid input!",
            icon: "error",
            confirmButtonText: "OK"
    });
    console.log("Invalid input");
        </script>';
    }
}

// Display logged-in successfully message
if (isset($_SESSION["login"]) && $_SESSION["login"] === true) {
    $loggedInUser = $_SESSION["name"];
    echo '
        <script>
            Swal.fire({
            title: "Congrats",
            text: "Logged in successfully. Welcome, ' . $loggedInUser . '!",
            icon: "success",
            confirmButtonText: "OK"
        });
            console.log(\'Log In Successful\');
        </script>';

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
                <h2 class="mt-3 text-center">Login</h2>
                <form class="d-flex justify-content-center flex-column" action="" method="post" autocomplete="off">
                    <div class="form-group">
                        <label for="fullnameemail">Fullname or Email:</label>
                        <input type="text" class="form-control" name="fullnameemail" id="fullnameemail" required
                            value="">
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" class="form-control" name="password" id="password" required value="">
                    </div>
                    <br>
                    <button type="submit" class="btn btn-primary" name="submit">Login</button>
                </form>
                <br>
                <div class="register-login">
                    <a href="Register.php" class="btn btn-primary">Register</a>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous">
</script>

</html>