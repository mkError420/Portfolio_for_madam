<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

require_once '../config/database.php';

// Initialize database connection
$database = new Database();
$pdo = $database->getConnection();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Add Album
        if (isset($_POST['add_album'])) {
            $title = $_POST['title'] ?? '';
            $artist = $_POST['artist'] ?? '';
            $release_date = $_POST['release_date'] ?? '';
            $genre = $_POST['genre'] ?? '';
            $type = $_POST['type'] ?? 'album';
            
            $stmt = $pdo->prepare("INSERT INTO albums (title, artist, release_date, genre, type) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $artist, $release_date, $genre, $type]);
            $_SESSION['success'] = 'Album added successfully!';
        }
        
        // Add Tour Date
        if (isset($_POST['add_tour'])) {
            $venue = $_POST['venue'] ?? '';
            $city = $_POST['city'] ?? '';
            $date = $_POST['date'] ?? '';
            $status = $_POST['status'] ?? 'upcoming';
            
            $stmt = $pdo->prepare("INSERT INTO tour_dates (venue, city, date, status) VALUES (?, ?, ?, ?)");
            $stmt->execute([$venue, $city, $date, $status]);
            $_SESSION['success'] = 'Tour date added successfully!';
        }
        
        // Add Video
        if (isset($_POST['add_video'])) {
            $title = $_POST['title'] ?? '';
            $category = $_POST['category'] ?? '';
            $url = $_POST['url'] ?? '';
            
            $stmt = $pdo->prepare("INSERT INTO videos (title, category, url) VALUES (?, ?, ?)");
            $stmt->execute([$title, $category, $url]);
            $_SESSION['success'] = 'Video added successfully!';
        }
        
        // Add Gallery Image
        if (isset($_POST['add_gallery'])) {
            $title = $_POST['title'] ?? '';
            $category = $_POST['category'] ?? '';
            
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../uploads/gallery/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_name = time() . '_' . basename($_FILES['image']['name']);
                $target_path = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                    $image_url = '../uploads/gallery/' . $file_name;
                    $stmt = $pdo->prepare("INSERT INTO gallery (title, category, image_url) VALUES (?, ?, ?)");
                    $stmt->execute([$title, $category, $image_url]);
                    $_SESSION['success'] = 'Gallery image added successfully!';
                } else {
                    $_SESSION['error'] = 'Failed to upload image';
                }
            } else {
                $_SESSION['error'] = 'Please select an image file';
            }
        }
        
        // Handle file uploads
        if (isset($_POST['upload_files'])) {
            $upload_dir = '../uploads/';
            $category = $_POST['upload_category'] ?? '';
            $uploaded_files = [];
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            foreach ($_FILES['files']['name'] as $key => $name) {
                if ($_FILES['files']['error'][$key] === UPLOAD_ERR_OK) {
                    $file_name = time() . '_' . basename($name);
                    $target_path = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($_FILES['files']['tmp_name'][$key], $target_path)) {
                        $uploaded_files[] = $file_name;
                    }
                }
            }
            
            if (!empty($uploaded_files)) {
                $_SESSION['success'] = count($uploaded_files) . ' files uploaded successfully!';
            } else {
                $_SESSION['error'] = 'No files were uploaded';
            }
        }
        
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    }
    
    // Redirect to prevent form resubmission
    header('Location: admin_hub.php');
    exit();
}

// Get statistics
try {
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }
    
    $albums_count = $pdo->query("SELECT COUNT(*) FROM albums")->fetchColumn();
    $videos_count = $pdo->query("SELECT COUNT(*) FROM videos")->fetchColumn();
    $gallery_count = $pdo->query("SELECT COUNT(*) FROM gallery")->fetchColumn();
    $tour_count = $pdo->query("SELECT COUNT(*) FROM tour_dates")->fetchColumn();
    $messages_count = $pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
    
    // Get recent activity
    $recent_albums = $pdo->query("SELECT * FROM albums ORDER BY created_at DESC LIMIT 3")->fetchAll();
    $recent_videos = $pdo->query("SELECT * FROM videos ORDER BY created_at DESC LIMIT 3")->fetchAll();
    $recent_gallery = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC LIMIT 3")->fetchAll();
    $recent_tour = $pdo->query("SELECT * FROM tour_dates ORDER BY created_at DESC LIMIT 3")->fetchAll();
    
} catch (PDOException $e) {
    $error = 'Database connection error: ' . $e->getMessage();
    // Set default values
    $albums_count = 0;
    $videos_count = 0;
    $gallery_count = 0;
    $tour_count = 0;
    $messages_count = 0;
    $recent_albums = [];
    $recent_videos = [];
    $recent_gallery = [];
    $recent_tour = [];
} catch (Exception $e) {
    $error = $e->getMessage();
    // Set default values
    $albums_count = 0;
    $videos_count = 0;
    $gallery_count = 0;
    $tour_count = 0;
    $messages_count = 0;
    $recent_albums = [];
    $recent_videos = [];
    $recent_gallery = [];
    $recent_tour = [];
}
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
            color: #333;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 2rem;
            box-shadow: 2px 0 20px rgba(0, 0, 0, 0.1);
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 2rem;
            text-align: center;
        }

        .nav-section {
            margin-bottom: 2rem;
        }

        .nav-section-title {
            font-size: 0.9rem;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 0.8rem 1rem;
            margin-bottom: 0.5rem;
            color: #333;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .nav-item:hover {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            transform: translateX(5px);
        }

        .nav-item.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .nav-item i {
            margin-right: 0.8rem;
            width: 20px;
        }

        .main-content {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1.5rem 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
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
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .content-section {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .content-section.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1.5rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            animation: slideIn 0.5s ease;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .upload-area {
            border: 2px dashed #667eea;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            background: rgba(102, 126, 234, 0.05);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .upload-area:hover {
            background: rgba(102, 126, 234, 0.1);
            border-color: #764ba2;
        }

        .upload-area i {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 1rem;
        }

        .recent-activity {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .activity-item {
            padding: 1rem;
            border-bottom: 1px solid #e0e0e0;
            transition: background 0.3s ease;
        }

        .activity-item:hover {
            background: rgba(102, 126, 234, 0.05);
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .activity-meta {
            color: #666;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .admin-container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                padding: 1rem;
            }
            
            .main-content {
                padding: 1rem;
            }
            
            .header {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="logo">
                <i class="fas fa-music"></i> Admin Hub
            </div>
            
            <nav>
                <div class="nav-section">
                    <div class="nav-section-title">Main</div>
                    <a href="#" class="nav-item active" data-section="dashboard">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Content</div>
                    <a href="#" class="nav-item" data-section="music">
                        <i class="fas fa-music"></i> Music
                    </a>
                    <a href="#" class="nav-item" data-section="videos">
                        <i class="fas fa-video"></i> Videos
                    </a>
                    <a href="#" class="nav-item" data-section="gallery">
                        <i class="fas fa-images"></i> Gallery
                    </a>
                    <a href="#" class="nav-item" data-section="tour">
                        <i class="fas fa-calendar"></i> Tour
                    </a>
                    <a href="#" class="nav-item" data-section="messages">
                        <i class="fas fa-envelope"></i> Messages
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Management</div>
                    <a href="#" class="nav-item" data-section="upload">
                        <i class="fas fa-upload"></i> Upload Manager
                    </a>
                    <a href="#" class="nav-item" data-section="settings">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">System</div>
                    <a href="check_frontend.php" class="nav-item" target="_blank">
                        <i class="fas fa-external-link-alt"></i> View Site
                    </a>
                    <a href="logout.php" class="nav-item" style="color: #dc3545;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </nav>
        </aside>

        <main class="main-content">
            <div class="header">
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</h1>
                <div class="header-actions">
                    <a href="check_frontend.php" class="btn btn-secondary" target="_blank">
                        <i class="fas fa-external-link-alt"></i> View Site
                    </a>
                    <a href="logout.php" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php 
                    echo htmlspecialchars($_SESSION['success']); 
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?php 
                    echo htmlspecialchars($_SESSION['error']); 
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <!-- Dashboard Section -->
            <section id="dashboard" class="content-section active">
                <h2 style="color: white; margin-bottom: 2rem;">Dashboard Overview</h2>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-compact-disc"></i>
                        </div>
                        <div class="stat-number"><?php echo $albums_count; ?></div>
                        <div class="stat-label">Albums & Singles</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-video"></i>
                        </div>
                        <div class="stat-number"><?php echo $videos_count; ?></div>
                        <div class="stat-label">Videos</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-images"></i>
                        </div>
                        <div class="stat-number"><?php echo $gallery_count; ?></div>
                        <div class="stat-label">Gallery Images</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="stat-number"><?php echo $tour_count; ?></div>
                        <div class="stat-label">Tour Dates</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="stat-number"><?php echo $messages_count; ?></div>
                        <div class="stat-label">Messages</div>
                    </div>
                </div>

                <div class="recent-activity">
                    <h3 style="margin-bottom: 1.5rem;">Recent Activity</h3>
                    <?php if (!empty($recent_albums)): ?>
                        <?php foreach ($recent_albums as $album): ?>
                            <div class="activity-item">
                                <div class="activity-title">📀 <?php echo htmlspecialchars($album['title']); ?></div>
                                <div class="activity-meta">Album added on <?php echo date('M j, Y', strtotime($album['created_at'])); ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <?php if (!empty($recent_videos)): ?>
                        <?php foreach ($recent_videos as $video): ?>
                            <div class="activity-item">
                                <div class="activity-title">🎬 <?php echo htmlspecialchars($video['title']); ?></div>
                                <div class="activity-meta">Video added on <?php echo date('M j, Y', strtotime($video['created_at'])); ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <?php if (empty($recent_albums) && empty($recent_videos)): ?>
                        <div class="activity-item">
                            <div class="activity-title">No recent activity</div>
                            <div class="activity-meta">Start adding content to see activity here</div>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Music Section -->
            <section id="music" class="content-section">
                <h2 style="color: white; margin-bottom: 2rem;">Music Management</h2>
                
                <div class="form-container">
                    <h3 style="margin-bottom: 1.5rem;">Add New Album/Single</h3>
                    <form method="POST">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="title">Title *</label>
                                <input type="text" id="title" name="title" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="artist">Artist *</label>
                                <input type="text" id="artist" name="artist" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="release_date">Release Date</label>
                                <input type="date" id="release_date" name="release_date">
                            </div>
                            
                            <div class="form-group">
                                <label for="genre">Genre</label>
                                <input type="text" id="genre" name="genre" placeholder="Pop, Rock, etc.">
                            </div>
                            
                            <div class="form-group">
                                <label for="type">Type</label>
                                <select id="type" name="type">
                                    <option value="album">Album</option>
                                    <option value="single">Single</option>
                                    <option value="ep">EP</option>
                                </select>
                            </div>
                        </div>
                        
                        <button type="submit" name="add_album" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Album/Single
                        </button>
                    </form>
                </div>
            </section>

            <!-- Videos Section -->
            <section id="videos" class="content-section">
                <h2 style="color: white; margin-bottom: 2rem;">Video Management</h2>
                
                <div class="form-container">
                    <h3 style="margin-bottom: 1.5rem;">Add New Video</h3>
                    <form method="POST">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="video_title">Video Title *</label>
                                <input type="text" id="video_title" name="title" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="video_category">Category *</label>
                                <select id="video_category" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="music_video">Music Video</option>
                                    <option value="live_performance">Live Performance</option>
                                    <option value="behind_scenes">Behind the Scenes</option>
                                    <option value="interview">Interview</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="video_url">Video URL *</label>
                                <input type="url" id="video_url" name="url" placeholder="https://youtube.com/watch?v=..." required>
                            </div>
                        </div>
                        
                        <button type="submit" name="add_video" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Video
                        </button>
                    </form>
                </div>
            </section>

            <!-- Gallery Section -->
            <section id="gallery" class="content-section">
                <h2 style="color: white; margin-bottom: 2rem;">Gallery Management</h2>
                
                <div class="form-container">
                    <h3 style="margin-bottom: 1.5rem;">Add New Image</h3>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="image_title">Image Title *</label>
                                <input type="text" id="image_title" name="title" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="image_category">Category *</label>
                                <select id="image_category" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="performance">Performance</option>
                                    <option value="behind_scenes">Behind the Scenes</option>
                                    <option value="photoshoot">Photoshoot</option>
                                    <option value="fan_art">Fan Art</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="image">Image File *</label>
                                <input type="file" id="image" name="image" accept="image/*" required>
                            </div>
                        </div>
                        
                        <button type="submit" name="add_gallery" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Image
                        </button>
                    </form>
                </div>
            </section>

            <!-- Tour Section -->
            <section id="tour" class="content-section">
                <h2 style="color: white; margin-bottom: 2rem;">Tour Management</h2>
                
                <div class="form-container">
                    <h3 style="margin-bottom: 1.5rem;">Add New Tour Date</h3>
                    <form method="POST">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="venue">Venue *</label>
                                <input type="text" id="venue" name="venue" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="city">City *</label>
                                <input type="text" id="city" name="city" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="tour_date">Date *</label>
                                <input type="date" id="tour_date" name="date" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select id="status" name="status">
                                    <option value="upcoming">Upcoming</option>
                                    <option value="ongoing">Ongoing</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                        
                        <button type="submit" name="add_tour" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Tour Date
                        </button>
                    </form>
                </div>
            </section>

            <!-- Upload Manager Section -->
            <section id="upload" class="content-section">
                <h2 style="color: white; margin-bottom: 2rem;">Upload Manager</h2>
                
                <div class="form-container">
                    <h3 style="margin-bottom: 1.5rem;">Bulk File Upload</h3>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="upload_category">Upload Category</label>
                            <select id="upload_category" name="upload_category">
                                <option value="general">General</option>
                                <option value="music">Music Files</option>
                                <option value="images">Images</option>
                                <option value="videos">Videos</option>
                                <option value="documents">Documents</option>
                            </select>
                        </div>
                        
                        <div class="upload-area" onclick="document.getElementById('files').click()">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <h3>Click to upload or drag and drop</h3>
                            <p>Support for multiple file uploads</p>
                            <input type="file" id="files" name="files[]" multiple style="display: none;">
                        </div>
                        
                        <button type="submit" name="upload_files" class="btn btn-success" style="margin-top: 1rem;">
                            <i class="fas fa-upload"></i> Upload Files
                        </button>
                    </form>
                </div>
            </section>

            <!-- Messages Section -->
            <section id="messages" class="content-section">
                <h2 style="color: white; margin-bottom: 2rem;">Messages</h2>
                
                <div class="form-container">
                    <h3 style="margin-bottom: 1.5rem;">Contact Messages</h3>
                    <p style="color: #666;">Contact messages will appear here. This section can be expanded to show and manage incoming messages from your website's contact form.</p>
                </div>
            </section>

            <!-- Settings Section -->
            <section id="settings" class="content-section">
                <h2 style="color: white; margin-bottom: 2rem;">Settings</h2>
                
                <div class="form-container">
                    <h3 style="margin-bottom: 1.5rem;">Admin Settings</h3>
                    <p style="color: #666;">System settings and configuration options will be available here. This section can be expanded to include site settings, user management, and other administrative functions.</p>
                </div>
            </section>
        </main>
    </div>

    <script>
        // Navigation functionality
        document.addEventListener('DOMContentLoaded', function() {
            const navItems = document.querySelectorAll('.nav-item[data-section]');
            const sections = document.querySelectorAll('.content-section');
            
            navItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Remove active class from all nav items and sections
                    navItems.forEach(nav => nav.classList.remove('active'));
                    sections.forEach(section => section.classList.remove('active'));
                    
                    // Add active class to clicked nav item and corresponding section
                    this.classList.add('active');
                    const targetSection = document.getElementById(this.dataset.section);
                    if (targetSection) {
                        targetSection.classList.add('active');
                    }
                });
            });
            
            // File upload preview
            const fileInput = document.getElementById('files');
            const uploadArea = document.querySelector('.upload-area');
            
            if (fileInput && uploadArea) {
                fileInput.addEventListener('change', function() {
                    const files = this.files;
                    if (files.length > 0) {
                        uploadArea.innerHTML = `
                            <i class="fas fa-check-circle" style="color: #28a745;"></i>
                            <h3>${files.length} file(s) selected</h3>
                            <p>Click to change selection</p>
                        `;
                    }
                });
                
                // Drag and drop functionality
                uploadArea.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    this.style.background = 'rgba(102, 126, 234, 0.2)';
                });
                
                uploadArea.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    this.style.background = 'rgba(102, 126, 234, 0.05)';
                });
                
                uploadArea.addEventListener('drop', function(e) {
                    e.preventDefault();
                    this.style.background = 'rgba(102, 126, 234, 0.05)';
                    
                    const files = e.dataTransfer.files;
                    fileInput.files = files;
                    
                    if (files.length > 0) {
                        uploadArea.innerHTML = `
                            <i class="fas fa-check-circle" style="color: #28a745;"></i>
                            <h3>${files.length} file(s) selected</h3>
                            <p>Click to change selection</p>
                        `;
                    }
                });
            }
        });
    </script>
</body>
</html>
