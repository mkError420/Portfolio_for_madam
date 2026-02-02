<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_album':
                addAlbum($db);
                break;
            case 'add_tour_date':
                addTourDate($db);
                break;
            case 'add_video':
                addVideo($db);
                break;
            case 'add_gallery_image':
                addGalleryImage($db);
                break;
        }
    }
}

function addAlbum($db) {
    $title = $_POST['title'];
    $year = $_POST['year'];
    $description = $_POST['description'];
    
    $query = "INSERT INTO albums (title, year, description) VALUES (:title, :year, :description)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':year', $year);
    $stmt->bindParam(':description', $description);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Album added successfully!";
    } else {
        $_SESSION['error'] = "Failed to add album.";
    }
    
    header('Location: index.php');
    exit();
}

function addTourDate($db) {
    $date = $_POST['date'];
    $venue = $_POST['venue'];
    $city = $_POST['city'];
    $country = $_POST['country'];
    $ticket_url = $_POST['ticket_url'];
    $price_range = $_POST['price_range'];
    
    $query = "INSERT INTO tour_dates (date, venue, city, country, ticket_url, price_range) VALUES (:date, :venue, :city, :country, :ticket_url, :price_range)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':venue', $venue);
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':country', $country);
    $stmt->bindParam(':ticket_url', $ticket_url);
    $stmt->bindParam(':price_range', $price_range);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Tour date added successfully!";
    } else {
        $_SESSION['error'] = "Failed to add tour date.";
    }
    
    header('Location: index.php');
    exit();
}

function addVideo($db) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $video_id = $_POST['video_id'];
    $category = $_POST['category'];
    $duration = $_POST['duration'];
    $venue = $_POST['venue'];
    
    $query = "INSERT INTO videos (title, description, video_id, category, duration, venue) VALUES (:title, :description, :video_id, :category, :duration, :venue)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':video_id', $video_id);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':duration', $duration);
    $stmt->bindParam(':venue', $venue);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Video added successfully!";
    } else {
        $_SESSION['error'] = "Failed to add video.";
    }
    
    header('Location: index.php');
    exit();
}

function addGalleryImage($db) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $image_url = $_POST['image_url'];
    $category = $_POST['category'];
    
    $query = "INSERT INTO gallery (title, description, image_url, category) VALUES (:title, :description, :image_url, :category)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':image_url', $image_url);
    $stmt->bindParam(':category', $category);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Gallery image added successfully!";
    } else {
        $_SESSION['error'] = "Failed to add gallery image.";
    }
    
    header('Location: index.php');
    exit();
}

// Get statistics
$stats = [
    'albums' => $db->query("SELECT COUNT(*) as count FROM albums")->fetch(PDO::FETCH_ASSOC)['count'],
    'videos' => $db->query("SELECT COUNT(*) as count FROM videos")->fetch(PDO::FETCH_ASSOC)['count'],
    'gallery' => $db->query("SELECT COUNT(*) as count FROM gallery")->fetch(PDO::FETCH_ASSOC)['count'],
    'tour_dates' => $db->query("SELECT COUNT(*) as count FROM tour_dates WHERE status = 'upcoming'")->fetch(PDO::FETCH_ASSOC)['count'],
    'messages' => $db->query("SELECT COUNT(*) as count FROM contact_messages WHERE status = 'new'")->fetch(PDO::FETCH_ASSOC)['count'],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Hub - Singer Portfolio</title>
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
            color: #ffffff;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 2px 0 20px rgba(0, 0, 0, 0.1);
            padding: 2rem 0;
            overflow-y: auto;
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
            gap: 0.75rem;
        }

        .user-info {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            font-weight: bold;
        }

        .user-details h3 {
            color: #333;
            font-size: 1rem;
            margin: 0;
        }

        .user-details p {
            color: #666;
            font-size: 0.85rem;
            margin: 0;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-section {
            margin-bottom: 2rem;
        }

        .nav-section-title {
            color: #666;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0 1.5rem;
            font-weight: 600;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: #666;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            background: none;
            width: 100%;
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
            text-align: center;
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
            font-size: 2.5rem;
            font-weight: 300;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
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

        .btn-success {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b, #ff4757);
            color: white;
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
            text-align: center;
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

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .action-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .action-card:hover {
            transform: translateY(-5px);
        }

        .action-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #667eea;
        }

        .action-title {
            color: #333;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .action-description {
            color: #666;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }

        /* Recent Activity */
        .recent-activity {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .recent-activity h2 {
            color: #333;
            margin-bottom: 1.5rem;
        }

        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .activity-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 8px;
            transition: transform 0.3s ease;
        }

        .activity-item:hover {
            transform: translateY(-2px);
        }

        .activity-info {
            flex: 1;
        }

        .activity-title {
            color: #333;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .activity-meta {
            color: #666;
            font-size: 0.85rem;
        }

        .activity-time {
            color: #999;
            font-size: 0.75rem;
        }

        /* Logout */
        .logout-section {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .admin-container {
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

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .quick-actions {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-music"></i> Admin Hub</h2>
            </div>
            
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-details">
                    <h3><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></h3>
                    <p>Administrator</p>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Main</div>
                    <a href="dashboard.php" class="nav-item">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="index.php" class="nav-item active">
                        <i class="fas fa-home"></i> Admin Hub
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Content</div>
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
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Management</div>
                    <a href="upload_manager.php" class="nav-item">
                        <i class="fas fa-upload"></i> Upload Manager
                    </a>
                    <a href="messages.php" class="nav-item">
                        <i class="fas fa-envelope"></i> Messages
                    </a>
                    <a href="settings.php" class="nav-item">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">System</div>
                    <a href="check_frontend.php" class="nav-item" target="_blank">
                        <i class="fas fa-external-link-alt"></i> View Site
                    </a>
                    <a href="logout.php" class="nav-item" style="color: #ff4757;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="header">
                <h1>Admin Hub</h1>
                <div class="header-actions">
                    <a href="check_frontend.php" class="btn btn-secondary" target="_blank">
                        <i class="fas fa-external-link-alt"></i> View Site
                    </a>
                    <a href="logout.php" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div style="background: linear-gradient(135deg, #43e97b, #38f9d7); color: white; padding: 1rem 1.5rem; border-radius: 10px; margin-bottom: 2rem;">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <!-- Statistics -->
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
                    <div class="stat-value"><?= $stats['videos'] ?></div>
                    <div class="stat-label">Videos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe, #00f2fe); color: white;">
                        <i class="fas fa-images"></i>
                    </div>
                    <div class="stat-value"><?= $stats['gallery'] ?></div>
                    <div class="stat-label">Gallery</div>
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
                    <?php
// Redirect to the new Admin Hub
header('Location: admin_hub.php');
exit();
?>
                    <div class="stat-value"><?= $stats['messages'] ?></div>
                    <div class="stat-label">New Messages</div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="action-title">Add Album</div>
                    <div class="action-description">Create new music album</div>
                    <a href="music.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Album
                    </a>
                </div>
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <div class="action-title">Add Tour Date</div>
                    <div class="action-description">Schedule new show</div>
                    <a href="tour.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Tour Date
                    </a>
                </div>
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-video-plus"></i>
                    </div>
                    <div class="action-title">Add Video</div>
                    <div class="action-description">Upload new video</div>
                    <a href="videos.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Video
                    </a>
                </div>
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-image-plus"></i>
                    </div>
                    <div class="action-title">Upload Image</div>
                    <div class="action-description">Add gallery image</div>
                    <a href="gallery.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Image
                    </a>
                </div>
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-upload"></i>
                    </div>
                    <div class="action-title">Upload Manager</div>
                    <div class="action-description">Bulk upload files</div>
                    <a href="upload_manager.php" class="btn btn-success">
                        <i class="fas fa-upload"></i> Upload Manager
                    </a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="recent-activity">
                <h2>Recent Activity</h2>
                <div class="activity-list">
                    <?php
                    // Get recent activity from database
                    $recent_albums = $db->query("SELECT title, created_at FROM albums ORDER BY created_at DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
                    $recent_tour = $db->query("SELECT venue, date FROM tour_dates ORDER BY date DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
                    $recent_messages = $db->query("SELECT name, created_at FROM contact_messages ORDER BY created_at DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (!empty($recent_albums)) {
                        foreach ($recent_albums as $album) {
                            echo '<div class="activity-item">';
                            echo '<div class="activity-info">';
                            echo '<div class="activity-title">' . htmlspecialchars($album['title']) . '</div>';
                            echo '<div class="activity-meta">';
                            echo '<span class="activity-time">' . date('M j, Y', strtotime($album['created_at'])) . '</span>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    }
                    
                    if (!empty($recent_tour)) {
                        foreach ($recent_tour as $tour) {
                            echo '<div class="activity-item">';
                            echo '<div class="activity-info">';
                            echo '<div class="activity-title">' . htmlspecialchars($tour['venue']) . '</div>';
                            echo '<div class="activity-meta">';
                            echo '<span class="activity-time">' . date('M j, Y', strtotime($tour['date'])) . '</span>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    }
                    
                    if (!empty($recent_messages)) {
                        foreach ($recent_messages as $message) {
                            echo '<div class="activity-item">';
                            echo '<div class="activity-info">';
                            echo '<div class="activity-title">' . htmlspecialchars($message['name']) . '</div>';
                            echo '<div class="activity-meta">';
                            echo '<span class="activity-time">' . date('M j, Y g:i A', strtotime($message['created_at'])) . '</span>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    }
                    
                    if (empty($recent_albums) && empty($recent_tour) && empty($recent_messages)) {
                        echo '<div class="activity-item">';
                        echo '<div class="activity-info">';
                        echo '<div class="activity-title">No recent activity</div>';
                        echo '<div class="activity-meta">';
                        echo '<span class="activity-time">Start adding content to see activity here</span>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
