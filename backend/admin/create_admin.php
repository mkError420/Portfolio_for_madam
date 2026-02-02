<?php
// Create admin user script
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>👤 Create Admin User</h1>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? 'admin';
    $password = $_POST['password'] ?? 'admin123';
    
    try {
        require_once '../config/database.php';
        $db = new Database();
        $connection = $db->getConnection();
        
        // Check if user exists
        $stmt = $connection->prepare("SELECT COUNT(*) FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        
        if ($stmt->fetchColumn() > 0) {
            // Update existing user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $connection->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
            $stmt->execute([$hashed_password, $username]);
            echo "<p style='color: green;'>✅ Admin user '$username' password updated!</p>";
        } else {
            // Create new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $connection->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $hashed_password]);
            echo "<p style='color: green;'>✅ Admin user '$username' created!</p>";
        }
        
        echo "<p><a href='login.php'>Go to Login →</a></p>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    }
} else {
?>
    <form method="POST">
        <h3>Create/Update Admin User</h3>
        <p>
            <label>Username:</label><br>
            <input type="text" name="username" value="admin" required><br><br>
            
            <label>Password:</label><br>
            <input type="password" name="password" value="admin123" required><br><br>
            
            <input type="submit" value="Create/Update Admin User">
        </p>
    </form>
    
    <hr>
    <h3>Quick Actions:</h3>
    <p>
        <a href="setup.php">🚀 Run Full Setup</a><br>
        <a href="debug.php">🔍 Run Debug</a><br>
        <a href="login.php">🔐 Try Login</a>
    </p>
<?php
}
?>
