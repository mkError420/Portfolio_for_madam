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
            case 'mark_read':
                $id = $_POST['id'] ?? 0;
                $connection->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ?")->execute([$id]);
                $_SESSION['success'] = "Message marked as read!";
                break;
                
            case 'mark_unread':
                $id = $_POST['id'] ?? 0;
                $connection->prepare("UPDATE contact_messages SET status = 'unread' WHERE id = ?")->execute([$id]);
                $_SESSION['success'] = "Message marked as unread!";
                break;
                
            case 'delete_message':
                $id = $_POST['id'] ?? 0;
                $connection->prepare("DELETE FROM contact_messages WHERE id = ?")->execute([$id]);
                $_SESSION['success'] = "Message deleted successfully!";
                break;
                
            case 'mark_all_read':
                $connection->prepare("UPDATE contact_messages SET status = 'read' WHERE status = 'unread'")->execute();
                $_SESSION['success'] = "All messages marked as read!";
                break;
                
            case 'delete_all_read':
                $connection->prepare("DELETE FROM contact_messages WHERE status = 'read'")->execute();
                $_SESSION['success'] = "All read messages deleted!";
                break;
        }
        
        header('Location: messages.php');
        exit();
    }
}

// Get messages
$messages = $connection->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Separate unread and read messages
$unread_messages = [];
$read_messages = [];

foreach ($messages as $message) {
    if ($message['status'] === 'unread') {
        $unread_messages[] = $message;
    } else {
        $read_messages[] = $message;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Admin Dashboard</title>
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
            position: relative;
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

        .notification-badge {
            position: absolute;
            right: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            background: #ff4757;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: bold;
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            color: #333;
            font-size: 2rem;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
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

        .btn-success {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Stats */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }

        /* Tabs */
        .tabs {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            display: flex;
            gap: 1rem;
        }

        .tab {
            padding: 0.75rem 1.5rem;
            background: none;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #666;
            font-size: 0.95rem;
            position: relative;
        }

        .tab.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .tab-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff4757;
            color: white;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 0.7rem;
            font-weight: bold;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Message Cards */
        .message-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .message-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            border-left: 4px solid transparent;
        }

        .message-card.unread {
            border-left-color: #667eea;
            background: rgba(102, 126, 234, 0.05);
        }

        .message-card:hover {
            transform: translateY(-2px);
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }

        .message-sender {
            flex: 1;
        }

        .message-name {
            color: #333;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .message-email {
            color: #666;
            font-size: 0.9rem;
        }

        .message-date {
            color: #999;
            font-size: 0.8rem;
            white-space: nowrap;
        }

        .message-subject {
            color: #333;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .message-content {
            color: #666;
            line-height: 1.5;
            margin-bottom: 1rem;
        }

        .message-actions {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
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

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .empty-state i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            color: #333;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: #666;
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

            .stats-row {
                grid-template-columns: 1fr;
            }

            .message-header {
                flex-direction: column;
                gap: 0.5rem;
            }

            .message-actions {
                flex-wrap: wrap;
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
                <a href="messages.php" class="nav-item active">
                    <i class="fas fa-envelope"></i> Messages
                    <?php if (count($unread_messages) > 0): ?>
                        <span class="notification-badge"><?= count($unread_messages) ?></span>
                    <?php endif; ?>
                </a>
                <a href="settings.php" class="nav-item">
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
                <h1>Contact Messages</h1>
                <div class="header-actions">
                    <?php if (count($unread_messages) > 0): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="mark_all_read">
                            <button type="submit" class="btn btn-success" onclick="return confirm('Mark all messages as read?')">
                                <i class="fas fa-check-double"></i> Mark All Read
                            </button>
                        </form>
                    <?php endif; ?>
                    <?php if (count($read_messages) > 0): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="delete_all_read">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Delete all read messages? This cannot be undone.')">
                                <i class="fas fa-trash-alt"></i> Delete All Read
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <!-- Stats -->
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-value"><?= count($messages) ?></div>
                    <div class="stat-label">Total Messages</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= count($unread_messages) ?></div>
                    <div class="stat-label">Unread</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= count($read_messages) ?></div>
                    <div class="stat-label">Read</div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="tabs">
                <button class="tab active" onclick="switchTab('all')">
                    All Messages
                    <span class="tab-badge"><?= count($messages) ?></span>
                </button>
                <button class="tab" onclick="switchTab('unread')">
                    Unread
                    <?php if (count($unread_messages) > 0): ?>
                        <span class="tab-badge"><?= count($unread_messages) ?></span>
                    <?php endif; ?>
                </button>
                <button class="tab" onclick="switchTab('read')">
                    Read
                    <span class="tab-badge"><?= count($read_messages) ?></span>
                </button>
            </div>

            <!-- All Messages Tab -->
            <div id="all-tab" class="tab-content active">
                <div class="message-list">
                    <?php foreach ($messages as $message): ?>
                        <div class="message-card <?= $message['status'] === 'unread' ? 'unread' : '' ?>">
                            <div class="message-header">
                                <div class="message-sender">
                                    <div class="message-name"><?= htmlspecialchars($message['name']) ?></div>
                                    <div class="message-email"><?= htmlspecialchars($message['email']) ?></div>
                                </div>
                                <div class="message-date"><?= date('M j, Y g:i A', strtotime($message['created_at'])) ?></div>
                            </div>
                            
                            <?php if ($message['subject']): ?>
                                <div class="message-subject"><?= htmlspecialchars($message['subject']) ?></div>
                            <?php endif; ?>
                            
                            <div class="message-content">
                                <?= nl2br(htmlspecialchars($message['message'])) ?>
                            </div>
                            
                            <div class="message-actions">
                                <?php if ($message['status'] === 'unread'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="mark_read">
                                        <input type="hidden" name="id" value="<?= $message['id'] ?>">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check"></i> Mark Read
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="mark_unread">
                                        <input type="hidden" name="id" value="<?= $message['id'] ?>">
                                        <button type="submit" class="btn btn-secondary">
                                            <i class="fas fa-envelope"></i> Mark Unread
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <a href="mailto:<?= htmlspecialchars($message['email']) ?>" class="btn btn-primary" target="_blank">
                                    <i class="fas fa-reply"></i> Reply
                                </a>
                                
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="delete_message">
                                    <input type="hidden" name="id" value="<?= $message['id'] ?>">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this message?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php if (empty($messages)): ?>
                        <div class="empty-state">
                            <i class="fas fa-envelope"></i>
                            <h3>No messages yet</h3>
                            <p>Messages from your contact form will appear here</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Unread Messages Tab -->
            <div id="unread-tab" class="tab-content">
                <div class="message-list">
                    <?php foreach ($unread_messages as $message): ?>
                        <div class="message-card unread">
                            <div class="message-header">
                                <div class="message-sender">
                                    <div class="message-name"><?= htmlspecialchars($message['name']) ?></div>
                                    <div class="message-email"><?= htmlspecialchars($message['email']) ?></div>
                                </div>
                                <div class="message-date"><?= date('M j, Y g:i A', strtotime($message['created_at'])) ?></div>
                            </div>
                            
                            <?php if ($message['subject']): ?>
                                <div class="message-subject"><?= htmlspecialchars($message['subject']) ?></div>
                            <?php endif; ?>
                            
                            <div class="message-content">
                                <?= nl2br(htmlspecialchars($message['message'])) ?>
                            </div>
                            
                            <div class="message-actions">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="mark_read">
                                    <input type="hidden" name="id" value="<?= $message['id'] ?>">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check"></i> Mark Read
                                    </button>
                                </form>
                                
                                <a href="mailto:<?= htmlspecialchars($message['email']) ?>" class="btn btn-primary" target="_blank">
                                    <i class="fas fa-reply"></i> Reply
                                </a>
                                
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="delete_message">
                                    <input type="hidden" name="id" value="<?= $message['id'] ?>">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this message?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php if (empty($unread_messages)): ?>
                        <div class="empty-state">
                            <i class="fas fa-envelope-open"></i>
                            <h3>No unread messages</h3>
                            <p>All messages have been read</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Read Messages Tab -->
            <div id="read-tab" class="tab-content">
                <div class="message-list">
                    <?php foreach ($read_messages as $message): ?>
                        <div class="message-card">
                            <div class="message-header">
                                <div class="message-sender">
                                    <div class="message-name"><?= htmlspecialchars($message['name']) ?></div>
                                    <div class="message-email"><?= htmlspecialchars($message['email']) ?></div>
                                </div>
                                <div class="message-date"><?= date('M j, Y g:i A', strtotime($message['created_at'])) ?></div>
                            </div>
                            
                            <?php if ($message['subject']): ?>
                                <div class="message-subject"><?= htmlspecialchars($message['subject']) ?></div>
                            <?php endif; ?>
                            
                            <div class="message-content">
                                <?= nl2br(htmlspecialchars($message['message'])) ?>
                            </div>
                            
                            <div class="message-actions">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="mark_unread">
                                    <input type="hidden" name="id" value="<?= $message['id'] ?>">
                                    <button type="submit" class="btn btn-secondary">
                                        <i class="fas fa-envelope"></i> Mark Unread
                                    </button>
                                </form>
                                
                                <a href="mailto:<?= htmlspecialchars($message['email']) ?>" class="btn btn-primary" target="_blank">
                                    <i class="fas fa-reply"></i> Reply
                                </a>
                                
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="delete_message">
                                    <input type="hidden" name="id" value="<?= $message['id'] ?>">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this message?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php if (empty($read_messages)): ?>
                        <div class="empty-state">
                            <i class="fas fa-envelope-open-text"></i>
                            <h3>No read messages</h3>
                            <p>Read messages will appear here</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        function switchTab(tab) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.querySelectorAll('.tab').forEach(tabBtn => {
                tabBtn.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tab + '-tab').classList.add('active');
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
