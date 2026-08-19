<?php
session_start();
include('db.php'); // Include the database connection

// Handle form submission
if (isset($_POST['submit'])) {
    // Collect input data from the form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Hash the password before storing it
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if the email is already registered
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $error_message = "Email is already registered.";
        } else {
            // Insert user data into the database
            $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashed_password')";

            if ($conn->query($sql) === TRUE) {
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                header("Location: login.php"); // Redirect to login page after successful signup
                exit();
            } else {
                $error_message = "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
</head>
<body>

<h2>Sign Up</h2>

<?php
if (isset($error_message)) {
    echo "<p style='color:red;'>$error_message</p>";
}
?>

<form method="POST" action="">
    <label for="name">Full Name:</label>
    <input type="text" name="name" required><br>

    <label for="email">Email:</label>
    <input type="email" name="email" required><br>

    <label for="password">Password:</label>
    <input type="password" name="password" required><br>

    <label for="confirm_password">Confirm Password:</label>
    <input type="password" name="confirm_password" required><br>

    <button type="submit" name="submit">Sign Up</button>
</form>

<p>Already have an account? <a href="login.php">Log in</a></p>

</body>
</html>
