<?php
// Database connection
session_start();
include('db.php');

// Retrieve and sanitize inputs
$user_id = 1; // Replace with session user ID if available
$game_name = mysqli_real_escape_string($conn, $_POST['gameName']);
$game_price = floatval($_POST['gamePrice']);
$payment_method = mysqli_real_escape_string($conn, $_POST['paymentMethod']);

// Insert data into the payments table
$sql = "INSERT INTO payments (user_id, game_name, game_price, payment_method, payment_status) 
        VALUES ('$user_id', '$game_name', '$game_price', '$payment_method', 'pending')";

if ($conn->query($sql) === TRUE) {
    $payment_id = $conn->insert_id;

    // Update payment status to 'completed'
    $update_sql = "UPDATE payments SET payment_status = 'completed' WHERE id = '$payment_id'";
    if ($conn->query($update_sql) === TRUE) {
        echo "<script>alert('Payment successful! You have successfully purchased $game_name.');</script>";
        echo "<script>window.location.href = 'index.html';</script>"; // Redirect to the home page
    } else {
        echo "Error updating payment status: " . $conn->error;
    }
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close the connection
$conn->close();
?>
