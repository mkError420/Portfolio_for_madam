<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

require_once '../config/database.php';
require_once '../config/cors.php';

$db = new Database();
$connection = $db->getConnection();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'change_password':
                $current_password = $_POST['current_password'] ?? '';
                $new_password = $_POST['new_password'] ?? '';
                $confirm_password = $_POST['confirm_password'] ?? '';
                
                // Validate
                if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                    $_SESSION['error'] = "All fields are required";
                } elseif ($new_password !== $confirm_password) {
                    $_SESSION['error'] = "New passwords do not match";
                } elseif (strlen($new_password) < 8) {
                    $_SESSION['error'] = "Password must be at least 8 characters long";
                } else {
                    // Verify current password
                    $stmt = $connection->prepare("SELECT password FROM admin_users WHERE id = ?");
                    $stmt->execute([$_SESSION['admin_id']]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($user && password_verify($current_password, $user['password'])) {
                        // Update password
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $update_stmt = $connection->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
                        $update_stmt->execute([$hashed_password, $_SESSION['admin_id']]);
                        $_SESSION['success'] = "Password changed successfully!";
                    } else {
                        $_SESSION['error'] = "Current password is incorrect";
                    }
                }
                break;
                
            case 'update_profile':
                $username = $_POST['username'] ?? '';
                $email = $_POST['email'] ?? '';
                
                if (empty($username)) {
                    $_SESSION['error'] = "Username is required";
                } else {
                    $stmt = $connection->prepare("UPDATE admin_users SET username = ?, email = ? WHERE id = ?");
                    $stmt->execute([$username, $email, $_SESSION['admin_id']]);
                    $_SESSION['admin_username'] = $username;
                    $_SESSION['success'] = "Profile updated successfully!";
                }
                break;
                
            case 'add_admin':
                $username = $_POST['username'] ?? '';
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';
                
                if (empty($username) || empty($password)) {
                    $_SESSION['error'] = "Username and password are required";
                } elseif (strlen($password) < 8) {
                    $_SESSION['error'] = "Password must be at least 8 characters long";
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $connection->prepare("INSERT INTO admin_users (username, email, password) VALUES (?, ?, ?)");
                    $stmt->execute([$username, $email, $hashed_password]);
                    $_SESSION['success'] = "Admin user added successfully!";
                }
                break;
                
            case 'delete_admin':
                $id = $_POST['id'] ?? 0;
                if ($id == $_SESSION['admin_id']) {
                    $_SESSION['error'] = "You cannot delete your own account";
                } else {
                    $connection->prepare("DELETE FROM admin_users WHERE id = ?")->execute([$id]);
                    $_SESSION['success'] = "Admin user deleted successfully!";
                }
                break;
        }
        
        header('Location: settings.php');
        exit();
    }
}

// Get admin users
$admin_users = $connection->query("SELECT id, username, email, created_at FROM admin_users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Get current admin info
$current_admin = null;
foreach ($admin_users as $user) {
    if ($user['id'] == $_SESSION['admin_id']) {
        $current_admin = $user;
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 2px 0 20px rgba(0, 0, 0, 0.1);
            padding: 2rem 0;
        }

        .sidebar-header {
            padding: 0 1.5rem 2rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .sidebar-header h2 {
            color: #333;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-item {
            display: block;
            padding: 0.75rem 1.5rem;
            color: #666;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .nav-item:hover {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            transform: translateX(5px);
        }

        .nav-item.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .nav-item i {
            width: 20px;
            margin-right: 0.75rem;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 2rem;
            overflow-x: hidden;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            color: #333;
            font-size: 2rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.9);
            color: #666;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b, #ff4757);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Settings Grid */
        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
        }

        .settings-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .settings-card h2 {
            color: #333;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: border-color 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
        }

        /* Admin Users Table */
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .admin-table th,
        .admin-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .admin-table th {
            color: #333;
            font-weight: 600;
            background: rgba(102, 126, 234, 0.1);
        }

        .admin-table tr:hover {
            background: rgba(102, 126, 234, 0.05);
        }

        .current-user {
            color: #667eea;
            font-weight: 600;
        }

        /* Alert */
        .alert {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-error {
            background: linear-gradient(135deg, #ff6b6b, #ff4757);
        }

        /* Security Info */
        .security-info {
            background: rgba(102, 126, 234, 0.1);
            border-left: 4px solid #667eea;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
        }

        .security-info h4 {
            color: #333;
            margin-bottom: 0.5rem;
        }

        .security-info ul {
            color: #666;
            margin-left: 1.5rem;
        }

        .security-info li {
            margin-bottom: 0.25rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                padding: 1rem 0;
            }

            .sidebar-nav {
                display: flex;
                overflow-x: auto;
                padding: 0 1rem;
            }

            .nav-item {
                white-space: nowrap;
                padding: 0.5rem 1rem;
            }

            .main-content {
                padding: 1rem;
            }

            .settings-grid {
                grid-template-columns: 1fr;
            }

            .admin-table {
                font-size: 0.9rem;
            }

            .admin-table th,
            .admin-table td {
                padding: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-music"></i> Admin Panel</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="music.php" class="nav-item">
                    <i class="fas fa-compact-disc"></i> Music
                </a>
                <a href="videos.php" class="nav-item">
                    <i class="fas fa-video"></i> Videos
                </a>
                <a href="gallery.php" class="nav-item">
                    <i class="fas fa-images"></i> Gallery
                </a>
                <a href="tour.php" class="nav-item">
                    <i class="fas fa-calendar-alt"></i> Tour
                </a>
                <a href="messages.php" class="nav-item">
                    <i class="fas fa-envelope"></i> Messages
                </a>
                <a href="settings.php" class="nav-item active">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <a href="logout.php" class="nav-item" style="margin-top: 2rem; color: #ff4757;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="header">
                <h1>Settings</h1>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($_SESSION['error']) ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="settings-grid">
                <!-- Profile Settings -->
                <div class="settings-card">
                    <h2><i class="fas fa-user"></i> Profile Settings</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="form-group">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-input" value="<?= htmlspecialchars($current_admin['username'] ?? '') ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-input" value="<?= htmlspecialchars($current_admin['email'] ?? '') ?>">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                    </form>
                </div>

                <!-- Password Settings -->
                <div class="settings-card">
                    <h2><i class="fas fa-lock"></i> Change Password</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="form-group">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-input" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-input" minlength="8" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-input" minlength="8" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-key"></i> Change Password
                        </button>
                        
                        <div class="security-info">
                            <h4>Password Requirements:</h4>
                            <ul>
                                <li>At least 8 characters long</li>
                                <li>Use a mix of letters, numbers, and symbols</li>
                                <li>Avoid common passwords</li>
                            </ul>
                        </div>
                    </form>
                </div>

                <!-- Admin Users -->
                <div class="settings-card" style="grid-column: 1 / -1;">
                    <h2><i class="fas fa-users"></i> Admin Users</h2>
                    
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($admin_users as $user): ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars($user['username']) ?>
                                        <?php if ($user['id'] == $_SESSION['admin_id']): ?>
                                            <span class="current-user">(You)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($user['email'] ?? 'N/A') ?></td>
                                    <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                    <td>
                                        <?php if ($user['id'] != $_SESSION['admin_id']): ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="delete_admin">
                                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this admin user?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span style="color: #666;">Current user</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
