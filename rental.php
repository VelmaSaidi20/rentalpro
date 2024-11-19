<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'vel_db';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
session_start();

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: rental.php");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION['user'] = $result->fetch_assoc();
    } else {
        echo "Invalid credentials!";
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_property'])) {
    $address = $_POST['address'];
    $type = $_POST['type'];
    $rent_amount = $_POST['rent_amount'];
    $landlord_id = $_SESSION['user']['id'];

    $sql = "INSERT INTO properties (landlord_id, address, type, rent_amount) 
            VALUES ('$landlord_id', '$address', '$type', '$rent_amount')";
    $conn->query($sql);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_tenant'])) {
    $property_id = $_POST['property_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $sql = "INSERT INTO tenants (property_id, name, email, phone) 
            VALUES ('$property_id', '$name', '$email', '$phone')";
    $conn->query($sql);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_lease'])) {
    $property_id = $_POST['property_id'];
    $tenant_id = $_POST['tenant_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $rent_amount = $_POST['rent_amount'];

    $sql = "INSERT INTO leases (property_id, tenant_id, start_date, end_date, rent_amount) 
            VALUES ('$property_id', '$tenant_id', '$start_date', '$end_date', '$rent_amount')";
    $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Property Rental Management</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form, table { margin-bottom: 20px; }
        table { border-collapse: collapse; width: 100%; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 8px; text-align: left; }
        h2 { color: #444; }
    </style>
	<link rel="stylesheet" href="velma.css">
</head>
<body>
    <?php if (!isset($_SESSION['user'])): ?>
        <h2>Login</h2>
        <form method="POST">
            <label>Email:</label>
            <input type="email" name="email" required>
            <label>Password:</label>
            <input type="password" name="password" required>
            <button type="submit" name="login">Login</button>
        </form>
    <?php else: ?>
        <h2>Welcome, <?php echo $_SESSION['user']['name']; ?>!</h2>
        <a href="?logout">Logout</a>
        <h2>Add Property</h2>
        <form method="POST">
            <label>Address:</label>
            <input type="text" name="address" required>
            <label>Type:</label>
            <input type="text" name="type" required>
            <label>Rent Amount:</label>
            <input type="number" name="rent_amount" required>
            <button type="submit" name="add_property">Add Property</button>
        </form>
        <h2>Add Tenant</h2>
        <form method="POST">
            <label>Property ID:</label>
            <input type="number" name="property_id" required>
            <label>Name:</label>
            <input type="text" name="name" required>
            <label>Email:</label>
            <input type="email" name="email" required>
            <label>Phone:</label>
            <input type="text" name="phone" required>
            <button type="submit" name="add_tenant">Add Tenant</button>
        </form>
        <h2>Add Lease</h2>
        <form method="POST">
            <label>Property ID:</label>
            <input type="number" name="property_id" required>
            <label>Tenant ID:</label>
            <input type="number" name="tenant_id" required>
            <label>Start Date:</label>
            <input type="date" name="start_date" required>
            <label>End Date:</label>
            <input type="date" name="end_date" required>
            <label>Rent Amount:</label>
            <input type="number" name="rent_amount" required>
            <button type="submit" name="add_lease">Add Lease</button>
        </form>
        <h2>Properties</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Address</th>
                <th>Type</th>
                <th>Rent</th>
            </tr>
            <?php
            $result = $conn->query("SELECT * FROM properties WHERE landlord_id=" . $_SESSION['user']['id']);
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>{$row['id']}</td><td>{$row['address']}</td><td>{$row['type']}</td><td>{$row['rent_amount']}</td></tr>";
            }
            ?>
        </table>
        <h2>Tenants</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Property ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
            </tr>
            <?php
            $result = $conn->query("SELECT * FROM tenants");
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>{$row['id']}</td><td>{$row['property_id']}</td><td>{$row['name']}</td><td>{$row['email']}</td><td>{$row['phone']}</td></tr>";
            }
            ?>
        </table>
    <?php endif; ?>
</body>
</html>
