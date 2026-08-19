<?php
session_start();
include('db.php'); // Include the database connection

if (isset($_POST['submit'])) {
    // Collect input data from the form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query to check if the user exists in the database
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password using password_verify
        if (password_verify($password, $user['password'])) {
            // Store user session data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            header("Location: index.html"); // Redirect to the index page
            exit();
        } else {
            $error_message = "Invalid credentials. Please try again.";
        }
    } else {
        $error_message = "User not found.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>

<h2>Login</h2>

<?php
if (isset($error_message)) {
    echo "<p style='color:red;'>$error_message</p>";
}
?>

<form method="POST" action="">
    <label for="email">Email:</label>
    <input type="email" name="email" required><br>

    <label for="password">Password:</label>
    <input type="password" name="password" required><br>

    <button type="submit" name="submit">Log In</button>
</form>

<p>Don't have an account? <a href="signup.php">Sign up</a></p>

</body>
</html>
