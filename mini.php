<?php
// Database connection parameters
$servername = "localhost"; // Replace with your database server name
$username_db = "your_actual_db_username"; // Replace with your actual database username
$password_db = "your_actual_db_password"; // Replace with your actual database password
$dbname = "info";

// Create a database connection
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the user wants to create a new account
    if (isset($_POST["register"])) {
        // Fetch user input
        $newUsername = $_POST["new_username"];
        $newPassword = $_POST["new_password"];

        // Validate the new username and password (add more validation as needed)
        // Use prepared statements to prevent SQL injection
        $stmtRegister = $conn->prepare("INSERT INTO userinfo (username, password) VALUES (?, ?)");
        $stmtRegister->bind_param("ss", $newUsername, $newPassword);

        if ($stmtRegister->execute()) {
            // Registration successful, you can redirect or show a success message
            $registrationSuccess = true;
        } else {
            $registrationError = "Registration failed. Please try again.";
        }

        $stmtRegister->close();
    } else {
        // Fetch user input for login
        $username = $_POST["username"];
        $password = $_POST["password"];

        // Validate the username and password (add more validation as needed)
        // Use prepared statements to prevent SQL injection
        $stmtLogin = $conn->prepare("SELECT * FROM userinfo WHERE username = ? AND password = ?");
        $stmtLogin->bind_param("ss", $username, $password);

        $stmtLogin->execute();

        // Fetch the result
        $result = $stmtLogin->get_result();

        if ($result->num_rows > 0) {
            // Redirect to m.html if login is successful
            header("Location: m.html");
            exit();
        } else {
            $loginError = "Invalid username or password";
        }

        $stmtLogin->close();
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
    body {
        font-family: 'Roboto', sans-serif;
        background-color: #282c35; /* Dark background */
        color: #fff;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
    }

    form {
        background-color: #353b48; /* Darker background */
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        width: 300px;
        text-align: center;
        margin: 20px;
        transition: background-color 0.5s ease;
    }

    label {
        display: block;
        margin-bottom: 10px;
        color: #fff;
    }

    input {
        width: 100%;
        padding: 8px;
        margin-bottom: 15px;
        box-sizing: border-box;
        border: 1px solid #7f8c8d; /* Dark gray border */
        border-radius: 4px;
        background-color: #2f3640; /* Darker input background */
        color: #ecf0f1; /* Light text color */
    }

    button {
        background-color: #3498db; /* Blue button color */
        color: #fff;
        padding: 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #2980b9; /* Darker blue on hover */
    }

    .error {
        color: #e74c3c; /* Red error text */
        margin-bottom: 10px;
    }

    .success {
        color: #2ecc71; /* Green success text */
        margin-bottom: 10px;
    }
</style>
</head>
<body>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <h2>Login</h2>

    <?php if (isset($loginError)) { ?>
        <p class="error"><?php echo $loginError; ?></p>
    <?php } ?>

    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>

    <button type="submit">Login</button>
</form>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <h2>Register</h2>

    <?php if (isset($registrationError)) { ?>
        <p class="error"><?php echo $registrationError; ?></p>
    <?php } ?>

    <?php if (isset($registrationSuccess)) { ?>
        <p class="success">Registration successful! You can now login.</p>
    <?php } ?>

    <label for="new_username">New Username:</label>
    <input type="text" id="new_username" name="new_username" required>

    <label for="new_password">New Password:</label>
    <input type="password" id="new_password" name="new_password" required>

    <button type="submit" name="register">Register</button>
</form>

</body>
</html>