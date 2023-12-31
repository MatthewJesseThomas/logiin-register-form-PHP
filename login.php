<?php
require 'config.php';

function logInEmployee($employeeId, $employeeName)
{
    // Store the logged-in employee's information in a log file or database
    // In this example, we will simply log the message to the PHP error log
    error_log("Employee with ID $employeeId and name $employeeName logged in.");
}

function isEmployeeClockedIn($employeeId)
{
    global $conn;

    // Prepare the SQL statement to check if the employee is already clocked in
    $sql = "SELECT clock_in FROM attendance WHERE employee_id = ? AND DATE(clock_in) = CURDATE()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $employeeId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result->num_rows > 0;
}

function clockInEmployee($employeeId)
{
    global $conn;

    // Prepare the SQL statement to insert the employee's clock-in time
    $sql = "INSERT INTO attendance (employee_id, clock_in) VALUES (?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $employeeId);
    $stmt->execute();
    $stmt->close();
}

function logOutEmployee($employeeId)
{
    global $conn;

    // Check if the employee is already clocked out
    $sql = "SELECT clock_out FROM attendance WHERE employee_id = ? AND DATE(clock_out) = CURDATE()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $employeeId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Employee is already clocked out, display a message
        echo '
            <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Warning",
                    text: "You are already clocked out!",
                    icon: "warning",
                    confirmButtonText: "OK"
                });
            });
            console.log("Already Clocked Out");
            </script>';
    } else {
        // Prepare the SQL statement to update the clock-out time
        $sql = "UPDATE attendance SET clock_out = NOW() WHERE employee_id = ? AND DATE(clock_in) = CURDATE()";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $employeeId);
        $stmt->execute();

        // Check if the clock-out was successful
        if ($stmt->affected_rows > 0) {
            echo '
                <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Success",
                        text: "Clocked out successfully!",
                        icon: "success",
                        confirmButtonText: "OK"
                    });
                });
                console.log("Log Out Successful");
                </script>';
        } else {
            echo '
                <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Error",
                        text: "Failed to clock out!",
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                });
                console.log("Log Out Failed");
                </script>';
        }

        // Close the statement
        $stmt->close();
    }
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
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Oops",
                        text: "Incorrect Password!",
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                });
                    console.log("Incorrect Password");
                </script>';
            }
        } else {
            echo '
                <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Oops",
                        text: "User Not Registered!!!",
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                });
                    console.log("User Not Registered");
                </script>';
        }
    } else {
        echo '
            <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Oops",
                    text: "Invalid input!",
                    icon: "error",
                    confirmButtonText: "OK"
                });
            });
                console.log("Invalid input");
            </scrip>';
    }
}

// Display logged-In successfully message
if (isset($_SESSION["login"]) && $_SESSION["login"] === true) {
    $loggedInUser = $_SESSION["name"];
    $employeeId = $_SESSION["id"];

    // Check if the employee is already clocked in
    if (isEmployeeClockedIn($employeeId)) {
        echo '
            <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Warning",
                    text: "You are already clocked in!",
                    icon: "warning",
                    confirmButtonText: "OK"
                });
            });
            console.log("Already Clocked In");
            </script>';
    } else {
        // Clock employee's time-in
        clockInEmployee($employeeId);

        echo '
            <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Congrats",
                    text: "Clocked in successfully. Welcome, ' . $loggedInUser . '!",
                    icon: "success",
                    confirmButtonText: "OK"
                });
            });
            console.log("Log In Successful");
            </script>';

        // Redirect the user to the home page after clocking in
        header("Location: https://dev-clockops.pantheonsite.io/");
        exit();
    }
}

// Display logged-Out successfully message
if (isset($_POST['logout'])) {
    if (isset($_SESSION["login"]) && $_SESSION["login"] === true) {
        $employeeId = $_SESSION["id"];

        logOutEmployee($employeeId); // Call the function to log out the employee

        // Unset the session variables and destroy the session
        session_unset();
        session_destroy();

        // Redirect the user to the login page
        header("Location: login.php");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.min.css" />
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
                    <button type="submit" class="btn btn-primary" name="submit">Cloc</button>
                </form>
                <br>
                <form class="d-flex justify-content-center flex-column" action="" method="post">
                    <button type="submit" class="btn btn-primary mb-3 w-75" name="logout">ClockOutkIn</button>
                </form>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.min.js">
</script>

</html>