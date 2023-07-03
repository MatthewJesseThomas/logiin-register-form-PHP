<?php
require 'config.php';

function logInEmployee($employeeId, $employeeName)
{
    // Store the logged-in employee's information in a log file or database
    // In this example, we will simply log the message to the PHP error log
    error_log("Employee with ID $employeeId and name $employeeName logged in.");
}

if (isset($_POST['submit'])) {
    $fullname = $_POST['fullname'];
    $role = $_POST['role'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword'];

    $duplicate = mysqli_query($conn, "SELECT * FROM employee WHERE fullname = '$fullname' OR email = '$email'");
    if (mysqli_num_rows($duplicate) > 0) {
        echo '
         <script>
            Swal.fire({
            title: "Oops",
            text: "User or Email Already Taken!!!",
            icon: "error",
            confirmButtonText: "OK"
    });
    console.log("User or Email Already Taken");
        </script>';
    } else {
        if ($password == $confirmpassword) {
            // Hash the password before storing it in the database
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $query = "INSERT INTO employee(employee_role,fullname, email, phone, password) VALUES ('$role', '$fullname', '$email', '$phone', '$hashedPassword')";
            $result = mysqli_query($conn, $query);

            if ($result) {
                echo'
                <script>
                    Swal.fire({
                    title: "Congrats",
                    text: "Registration Successful",
                    icon: "success",
                    confirmButtonText: "OK"
        });
        console.log(\'Registration Successful\');
                </script>';


                // Log in the user after successful registration
                $employeeId = mysqli_insert_id($conn); // Get the employee_id of the inserted employee
                $_SESSION['employee_id'] = $employeeId;
                $_SESSION['fullname'] = $fullname;

                logInEmployee($employeeId, $fullname); // Call the function to log the message

                header("Location: login.php"); // Redirect to the welcome page after successful registration
                exit();
            } else {
                echo '
                <script> 
                    Swal.fire({
                    title: "Oops",
                    text: "Error Occurred During Registration!!!",
                    icon: "error",
                    confirmButtonText: "OK"
        });
            console.log(\'Error occurred during registration\');
                </script>';

            }
        } else {
            echo '
                <script> 
                    Swal.fire({
                    title: "Oops",
                    text: "Password Does Not Match",
                    icon: "error",
                    confirmButtonText: "OK"
        });
            console.log(\'Password Does Not Match\');
                </script>';
        }
    }
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
    <title>Registration</title>
</head>

<body>
    <div class="container mt-3">
        <div class="row mt-3">
            <div class="col-md-6 offset-md-3 registration-container mt-3">
                <h2 class="mt-3 text-center">Registration</h2>
                <form class="d-flex justify-content-center flex-column mt-3" action="" method="post" autocomplete="off">
                    <div class="form-group">
                        <label for="fullname">Fullname:</label>
                        <input type="text" class="form-control" name="fullname" id="fullname" required value="">
                    </div>
                    <div class="form-group">
                        <label for="role">Role:</label>
                        <input type="text" class="form-control" name="role" id="role" required value="">
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" name="email" id="email" required value="">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone No. :</label>
                        <input type="text" class="form-control" name="phone" id="phone" required value="">
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" class="form-control" name="password" id="password" required value="">
                    </div>
                    <div class="form-group">
                        <label for="confirmpassword">Confirm Password:</label>
                        <input type="password" class="form-control" name="confirmpassword" id="confirmpassword" required
                            value="">
                    </div>
                    <br>
                    <button type="submit" class="btn btn-primary" name="submit">Register</button>
                </form>
                <br>
                <div class="register-login w-25">
                    <a href="login.php" class="btn btn-primary">Login</a>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous">
</script>

</html>