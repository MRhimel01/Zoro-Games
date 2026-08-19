<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "game_store";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all payment history with user details
$paymentsQuery = "
    SELECT 
        p.id AS payment_id, 
        u.name AS buyer_name, 
        u.email AS buyer_email, 
        p.game_name, 
        p.game_price, 
        p.payment_method, 
        p.payment_status, 
        p.payment_date 
    FROM payments p
    LEFT JOIN users u ON p.user_id = u.id
    ORDER BY p.payment_date DESC";
$paymentsResult = $conn->query($paymentsQuery);

// Calculate total revenue (only for completed payments)
$totalRevenueQuery = "SELECT SUM(game_price) AS total_revenue FROM payments WHERE payment_status = 'completed'";
$totalRevenueResult = $conn->query($totalRevenueQuery);
$totalRevenue = $totalRevenueResult->fetch_assoc()['total_revenue'] ?? 0;

// Count total users (buyers and admins)
$totalUsersQuery = "SELECT COUNT(*) AS total_users FROM users";
$totalUsersResult = $conn->query($totalUsersQuery);
$totalUsers = $totalUsersResult->fetch_assoc()['total_users'] ?? 0;

// Count total admins
$totalAdminsQuery = "SELECT COUNT(*) AS total_admins FROM users WHERE role = 'admin'";
$totalAdminsResult = $conn->query($totalAdminsQuery);
$totalAdmins = $totalAdminsResult->fetch_assoc()['total_admins'] ?? 0;

// Close the connection
$conn->close();
?>

<!doctype html>
<html lang="en">

<head>
<link rel="stylesheet" href="css/admin.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - Payments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <style>
        .stat-card {
            background-color: #007bff;
            color: #fff;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
        }
        .table-container {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Dashboard</a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <!-- Total Revenue -->
            <div class="col-md-4">
                <div class="stat-card">
                    <h3>Total Revenue</h3>
                    <h2>$<?php echo number_format($totalRevenue, 2); ?></h2>
                </div>
            </div>
            <!-- Total Users -->
            <div class="col-md-4">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <h2><?php echo $totalUsers; ?></h2>
                </div>
            </div>
            <!-- Total Admins -->
            <div class="col-md-4">
                <div class="stat-card">
                    <h3>Total Admins</h3>
                    <h2><?php echo $totalAdmins; ?></h2>
                </div>
            </div>
        </div>

        <div class="table-container">
            <!-- Payment History -->
            <div class="card">
                <div class="card-header">
                    <h4>Payment History</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Payment ID</th>
                                <th>Buyer Name</th>
                                <th>Buyer Email</th>
                                <th>Game Name</th>
                                <th>Price</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                <th>Payment Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($paymentsResult->num_rows > 0) {
                                while ($row = $paymentsResult->fetch_assoc()) {
                                    echo "<tr>
                                            <td>" . htmlspecialchars($row['payment_id']) . "</td>
                                            <td>" . htmlspecialchars($row['buyer_name'] ?? 'N/A') . "</td>
                                            <td>" . htmlspecialchars($row['buyer_email'] ?? 'N/A') . "</td>
                                            <td>" . htmlspecialchars($row['game_name']) . "</td>
                                            <td>$" . number_format($row['game_price'], 2) . "</td>
                                            <td>" . htmlspecialchars($row['payment_method']) . "</td>
                                            <td>" . htmlspecialchars(ucfirst($row['payment_status'])) . "</td>
                                            <td>" . htmlspecialchars($row['payment_date']) . "</td>
                                        </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center'>No payment history available.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
