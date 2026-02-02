<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

require_once '../config/database.php';
require_once '../config/cors.php';

// Get dashboard statistics
$db = new Database();
$connection = $db->getConnection();

// Count records
$stats = [
    'albums' => $connection->query("SELECT COUNT(*) as count FROM albums")->fetch(PDO::FETCH_ASSOC)['count'],
    'singles' => $connection->query("SELECT COUNT(*) as count FROM singles")->fetch(PDO::FETCH_ASSOC)['count'],
    'videos' => $connection->query("SELECT COUNT(*) as count FROM videos")->fetch(PDO::FETCH_ASSOC)['count'],
    'gallery' => $connection->query("SELECT COUNT(*) as count FROM gallery")->fetch(PDO::FETCH_ASSOC)['count'],
    'tour_dates' => $connection->query("SELECT COUNT(*) as count FROM tour_dates")->fetch(PDO::FETCH_ASSOC)['count'],
    'messages' => $connection->query("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'unread'")->fetch(PDO::FETCH_ASSOC)['count']
];

// Get recent activity
$recent_albums = $connection->query("SELECT title, created_at FROM albums ORDER BY created_at DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
$recent_messages = $connection->query("SELECT name, email, created_at FROM contact_messages ORDER BY created_at DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
$upcoming_shows = $connection->query("SELECT venue, city, date FROM tour_dates WHERE date >= CURDATE() ORDER BY date ASC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Singer Portfolio</title>
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
            align-items: center;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #666;
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

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
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

        /* Recent Activity */
        .activity-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .activity-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .activity-card h3 {
            color: #333;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .activity-item {
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            color: #666;
            font-size: 0.9rem;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-item strong {
            color: #333;
            display: block;
            margin-bottom: 0.25rem;
        }

        .activity-item small {
            color: #999;
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

            .header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
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
                <a href="dashboard.php" class="nav-item active">
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
                    <?php if ($stats['messages'] > 0): ?>
                        <span style="background: #ff4757; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.8rem; margin-left: auto;">
                            <?= $stats['messages'] ?>
                        </span>
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
                <h1>Dashboard Overview</h1>
                <div class="header-actions">
                    <div class="user-info">
                        <i class="fas fa-user-circle"></i>
                        <span><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></span>
                    </div>
                    <a href="../index.php" class="btn btn-secondary" target="_blank">
                        <i class="fas fa-external-link-alt"></i> View Site
                    </a>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white;">
                        <i class="fas fa-compact-disc"></i>
                    </div>
                    <div class="stat-value"><?= $stats['albums'] ?></div>
                    <div class="stat-label">Albums</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb, #f5576c); color: white;">
                        <i class="fas fa-music"></i>
                    </div>
                    <div class="stat-value"><?= $stats['singles'] ?></div>
                    <div class="stat-label">Singles</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe, #00f2fe); color: white;">
                        <i class="fas fa-video"></i>
                    </div>
                    <div class="stat-value"><?= $stats['videos'] ?></div>
                    <div class="stat-label">Videos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b, #38f9d7); color: white;">
                        <i class="fas fa-images"></i>
                    </div>
                    <div class="stat-value"><?= $stats['gallery'] ?></div>
                    <div class="stat-label">Gallery Images</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a, #fee140); color: white;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-value"><?= $stats['tour_dates'] ?></div>
                    <div class="stat-label">Tour Dates</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #ff6b6b, #feca57); color: white;">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="stat-value"><?= $stats['messages'] ?></div>
                    <div class="stat-label">Unread Messages</div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="activity-grid">
                <div class="activity-card">
                    <h3><i class="fas fa-compact-disc"></i> Recent Albums</h3>
                    <?php if (empty($recent_albums)): ?>
                        <div class="activity-item">
                            <strong>No albums yet</strong>
                            <small>Start by adding your first album</small>
                        </div>
                    <?php else: ?>
                        <?php foreach ($recent_albums as $album): ?>
                            <div class="activity-item">
                                <strong><?= htmlspecialchars($album['title']) ?></strong>
                                <small>Added on <?= date('M j, Y', strtotime($album['created_at'])) ?></small>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="activity-card">
                    <h3><i class="fas fa-envelope"></i> Recent Messages</h3>
                    <?php if (empty($recent_messages)): ?>
                        <div class="activity-item">
                            <strong>No messages yet</strong>
                            <small>Messages from contact form will appear here</small>
                        </div>
                    <?php else: ?>
                        <?php foreach ($recent_messages as $message): ?>
                            <div class="activity-item">
                                <strong><?= htmlspecialchars($message['name']) ?></strong>
                                <small><?= htmlspecialchars($message['email']) ?> • <?= date('M j, Y', strtotime($message['created_at'])) ?></small>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="activity-card">
                    <h3><i class="fas fa-calendar-alt"></i> Upcoming Shows</h3>
                    <?php if (empty($upcoming_shows)): ?>
                        <div class="activity-item">
                            <strong>No upcoming shows</strong>
                            <small>Add tour dates to see them here</small>
                        </div>
                    <?php else: ?>
                        <?php foreach ($upcoming_shows as $show): ?>
                            <div class="activity-item">
                                <strong><?= htmlspecialchars($show['venue']) ?></strong>
                                <small><?= htmlspecialchars($show['city']) ?> • <?= date('M j, Y', strtotime($show['date'])) ?></small>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
