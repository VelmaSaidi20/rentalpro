<?php
session_start();
include('config.php');
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: renting.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $_SESSION['user'] = $result->fetch_assoc();
    } else {
        $login_error = "Invalid credentials!";
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $register_error = "Email already exists!";
    } else {
        $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['user'] = ['name' => $name, 'email' => $email];  
            header("Location: renting.php");
            exit;
        } else {
            $register_error = "Error registering user.";
        }
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_property'])) {
    if (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
        $landlord_id = $_SESSION['user']['id'];
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $type = mysqli_real_escape_string($conn, $_POST['type']);
        $rent_amount = mysqli_real_escape_string($conn, $_POST['rent_amount']);

        $sql = "INSERT INTO properties (landlord_id, address, type, rent_amount) 
                VALUES ('$landlord_id', '$address', '$type', '$rent_amount')";
        $conn->query($sql);
    } else {
        echo "User not logged in!";
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_tenant'])) {
    $property_id = mysqli_real_escape_string($conn, $_POST['property_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    $sql = "INSERT INTO tenants (property_id, name, email, phone) 
            VALUES ('$property_id', '$name', '$email', '$phone')";
    $conn->query($sql);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_lease'])) {
    $property_id = mysqli_real_escape_string($conn, $_POST['property_id']);
    $tenant_id = mysqli_real_escape_string($conn, $_POST['tenant_id']);
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
    $rent_amount = mysqli_real_escape_string($conn, $_POST['rent_amount']);

    $sql = "INSERT INTO leases (property_id, tenant_id, start_date, end_date, rent_amount) 
            VALUES ('$property_id', '$tenant_id', '$start_date', '$end_date', '$rent_amount')";
    $conn->query($sql);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Property Rental Management</title>
    <link rel="stylesheet" href="velmaa.css">
</head>
<body>

    <?php if (!isset($_SESSION['user'])): ?>

        <!-- Login Form -->
        <h1>Login</h1>
        <?php if (isset($login_error)) { echo "<p style='color:red;'>$login_error</p>"; } ?>
        <form method="POST">
            <label>Email:</label>
            <input type="email" name="email" required>
            <label>Password:</label>
            <input type="password" name="password" required>
            <button type="submit" name="login">Login</button>
        </form>

        <p>Don't have an account? <a href="#register_form" onclick="document.getElementById('register_form').style.display='block';">Register here</a></p>

        <!-- Registration Form (Hidden) -->
        <div id="register_form" style="display:none;">
            <h2>Register</h2>
            <?php if (isset($register_error)) { echo "<p style='color:red;'>$register_error</p>"; } ?>
            <form method="POST">
                <label>Name:</label>
                <input type="text" name="name" required>
                <label>Email:</label>
                <input type="email" name="email" required>
                <label>Password:</label>
                <input type="password" name="password" required>
                <button type="submit" name="register">Register</button>
            </form>
        </div>

    <?php else: ?>

        <!-- Dashboard for Logged-in User -->
        <h1>Welcome, <?php echo $_SESSION['user']['name']; ?>!</h1>
        <a href="?logout">Logout</a>

        <h2>Property Rental Management Dashboard</h2>
        <ul>
            <li><a href="#add_property_form" onclick="document.getElementById('add_property_form').style.display='block';">Add Property</a></li>
            <li><a href="#add_tenant_form" onclick="document.getElementById('add_tenant_form').style.display='block';">Add Tenant</a></li>
            <li><a href="#add_lease_form" onclick="document.getElementById('add_lease_form').style.display='block';">Add Lease</a></li>
        </ul>

        <!-- Add Property Form -->
        <div id="add_property_form" style="display:none;">
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
        </div>

        <!-- Add Tenant Form -->
        <div id="add_tenant_form" style="display:none;">
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
        </div>

        <!-- Add Lease Form -->
        <div id="add_lease_form" style="display:none;">
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
        </div>

        <!-- Display Properties -->
        <h2>Properties</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Address</th>
                <th>Type</th>
                <th>Rent</th>
            </tr>
            <?php
            if (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
                $landlord_id = $_SESSION['user']['id'];
                $result = $conn->query("SELECT * FROM properties WHERE landlord_id=" . $landlord_id);
                while ($row = $result->fetch_assoc()) {
                    echo "<tr><td>{$row['id']}</td><td>{$row['address']}</td><td>{$row['type']}</td><td>{$row['rent_amount']}</td></tr>";
                }
            }
            ?>
        </table>

        <!-- Display Tenants -->
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
		<!-- Display Lease Data -->
    <h2>Lease Details</h2>
    <table border="1">
        <tr>
            <th>Lease ID</th>
            <th>Property Address</th>
            <th>Tenant Name</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Rent Amount</th>
        </tr>
        <?php
        $sql = "SELECT leases.id, properties.address, tenants.name AS tenant_name, leases.start_date, leases.end_date, leases.rent_amount 
                FROM leases
                JOIN properties ON leases.property_id = properties.id
                JOIN tenants ON leases.tenant_id = tenants.id";

        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row['id'] . "</td>
                        <td>" . $row['address'] . "</td>
                        <td>" . $row['tenant_name'] . "</td>
                        <td>" . $row['start_date'] . "</td>
                        <td>" . $row['end_date'] . "</td>
                        <td>" . $row['rent_amount'] . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No lease data found</td></tr>";
        }
        ?>
    </table>

<?php
$conn->close();
?>
		<?php endif; ?>

</body>
</html>

