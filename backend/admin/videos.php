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
            case 'add_video':
                $title = $_POST['title'] ?? '';
                $description = $_POST['description'] ?? '';
                $category = $_POST['category'] ?? 'music';
                $video_url = $_POST['video_url'] ?? '';
                $thumbnail_url = $_POST['thumbnail_url'] ?? '';
                $duration = $_POST['duration'] ?? '';
                $views = $_POST['views'] ?? '0';
                $release_date = $_POST['release_date'] ?? date('Y-m-d');
                
                // Extract YouTube video ID from URL
                $video_id = '';
                if (strpos($video_url, 'youtube.com/watch?v=') !== false) {
                    preg_match('/v=([^&]+)/', $video_url, $matches);
                    $video_id = $matches[1] ?? '';
                } elseif (strpos($video_url, 'youtu.be/') !== false) {
                    preg_match('/youtu\.be\/([^?]+)/', $video_url, $matches);
                    $video_id = $matches[1] ?? '';
                }
                
                $stmt = $connection->prepare("INSERT INTO videos (title, description, category, video_url, thumbnail_url, video_id, duration, views, release_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $description, $category, $video_url, $thumbnail_url, $video_id, $duration, $views, $release_date]);
                
                $_SESSION['success'] = "Video added successfully!";
                break;
                
            case 'delete_video':
                $id = $_POST['id'] ?? 0;
                $connection->prepare("DELETE FROM videos WHERE id = ?")->execute([$id]);
                $_SESSION['success'] = "Video deleted successfully!";
                break;
                
            case 'update_video':
                $id = $_POST['id'] ?? 0;
                $title = $_POST['title'] ?? '';
                $description = $_POST['description'] ?? '';
                $category = $_POST['category'] ?? 'music';
                $video_url = $_POST['video_url'] ?? '';
                $thumbnail_url = $_POST['thumbnail_url'] ?? '';
                $duration = $_POST['duration'] ?? '';
                $views = $_POST['views'] ?? '0';
                $release_date = $_POST['release_date'] ?? date('Y-m-d');
                
                // Extract YouTube video ID
                $video_id = '';
                if (strpos($video_url, 'youtube.com/watch?v=') !== false) {
                    preg_match('/v=([^&]+)/', $video_url, $matches);
                    $video_id = $matches[1] ?? '';
                } elseif (strpos($video_url, 'youtu.be/') !== false) {
                    preg_match('/youtu\.be\/([^?]+)/', $video_url, $matches);
                    $video_id = $matches[1] ?? '';
                }
                
                $stmt = $connection->prepare("UPDATE videos SET title = ?, description = ?, category = ?, video_url = ?, thumbnail_url = ?, video_id = ?, duration = ?, views = ?, release_date = ? WHERE id = ?");
                $stmt->execute([$title, $description, $category, $video_url, $thumbnail_url, $video_id, $duration, $views, $release_date, $id]);
                
                $_SESSION['success'] = "Video updated successfully!";
                break;
        }
        
        header('Location: videos.php');
        exit();
    }
}

// Get videos
$videos = $connection->query("SELECT * FROM videos ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Management - Admin Dashboard</title>
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

        /* Video Grid */
        .video-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }

        .video-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .video-card:hover {
            transform: translateY(-5px);
        }

        .video-thumbnail {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: linear-gradient(45deg, #f0f0f0 25%, transparent 25%, transparent 75%, #f0f0f0 75%, #f0f0f0),
                        linear-gradient(45deg, #f0f0f0 25%, transparent 25%, transparent 75%, #f0f0f0 75%, #f0f0f0);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
            position: relative;
        }

        .video-duration {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.8rem;
        }

        .video-info {
            padding: 1.5rem;
        }

        .video-title {
            color: #333;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .video-category {
            display: inline-block;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
        }

        .video-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .video-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            color: #999;
            font-size: 0.8rem;
        }

        .video-actions {
            display: flex;
            gap: 0.5rem;
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
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
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

        .form-input, .form-textarea, .form-select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: border-color 0.3s ease;
        }

        .form-input:focus, .form-textarea:focus, .form-select:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
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
            grid-column: 1 / -1;
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

            .video-grid {
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
                <a href="dashboard.php" class="nav-item">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="music.php" class="nav-item">
                    <i class="fas fa-compact-disc"></i> Music
                </a>
                <a href="videos.php" class="nav-item active">
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
                <h1>Video Management</h1>
                <button class="btn btn-primary" onclick="openModal()">
                    <i class="fas fa-plus"></i> Add Video
                </button>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <!-- Video Grid -->
            <div class="video-grid">
                <?php foreach ($videos as $video): ?>
                    <div class="video-card">
                        <div style="position: relative;">
                            <?php if ($video['thumbnail_url']): ?>
                                <img src="<?= htmlspecialchars($video['thumbnail_url']) ?>" alt="<?= htmlspecialchars($video['title']) ?>" class="video-thumbnail">
                            <?php else: ?>
                                <div class="video-thumbnail"></div>
                            <?php endif; ?>
                            <?php if ($video['duration']): ?>
                                <span class="video-duration"><?= htmlspecialchars($video['duration']) ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="video-info">
                            <h3 class="video-title"><?= htmlspecialchars($video['title']) ?></h3>
                            <span class="video-category"><?= htmlspecialchars(ucfirst($video['category'])) ?></span>
                            <?php if ($video['description']): ?>
                                <p class="video-description"><?= htmlspecialchars($video['description']) ?></p>
                            <?php endif; ?>
                            <div class="video-meta">
                                <span><i class="fas fa-eye"></i> <?= number_format($video['views']) ?> views</span>
                                <span><?= date('M j, Y', strtotime($video['release_date'])) ?></span>
                            </div>
                            <div class="video-actions">
                                <button class="btn btn-secondary" onclick="editVideo(<?= htmlspecialchars(json_encode($video)) ?>)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="delete_video">
                                    <input type="hidden" name="id" value="<?= $video['id'] ?>">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <?php if (empty($videos)): ?>
                    <div class="empty-state">
                        <i class="fas fa-video"></i>
                        <h3>No videos yet</h3>
                        <p>Start by adding your first video</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Add/Edit Video Modal -->
    <div id="videoModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle" style="margin-bottom: 1.5rem;">Add Video</h2>
            <form method="POST" id="videoForm">
                <input type="hidden" name="action" id="formAction" value="add_video">
                <input type="hidden" name="id" id="videoId">
                
                <div class="form-group">
                    <label class="form-label">Title *</label>
                    <input type="text" name="title" class="form-input" id="videoTitle" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-textarea" id="videoDescription"></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select" id="videoCategory">
                        <option value="music">Music Video</option>
                        <option value="live">Live Performance</option>
                        <option value="behind">Behind the Scenes</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Video URL *</label>
                    <input type="url" name="video_url" class="form-input" id="videoUrl" placeholder="https://www.youtube.com/watch?v=..." required>
                    <small style="color: #666; margin-top: 0.25rem; display: block;">YouTube URL (e.g., https://www.youtube.com/watch?v=VIDEO_ID)</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Thumbnail URL</label>
                    <input type="url" name="thumbnail_url" class="form-input" id="thumbnailUrl" placeholder="https://example.com/thumbnail.jpg">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Duration</label>
                    <input type="text" name="duration" class="form-input" id="videoDuration" placeholder="3:45">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Views</label>
                    <input type="number" name="views" class="form-input" id="videoViews" value="0" min="0">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Release Date</label>
                    <input type="date" name="release_date" class="form-input" id="releaseDate" value="<?= date('Y-m-d') ?>">
                </div>
                
                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Add Video</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('modalTitle').textContent = 'Add Video';
            document.getElementById('formAction').value = 'add_video';
            document.getElementById('submitBtn').textContent = 'Add Video';
            document.getElementById('videoForm').reset();
            document.getElementById('videoModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('videoModal').classList.remove('active');
        }

        function editVideo(video) {
            document.getElementById('modalTitle').textContent = 'Edit Video';
            document.getElementById('formAction').value = 'update_video';
            document.getElementById('submitBtn').textContent = 'Update Video';
            
            // Fill form with video data
            document.getElementById('videoId').value = video.id;
            document.getElementById('videoTitle').value = video.title;
            document.getElementById('videoDescription').value = video.description || '';
            document.getElementById('videoCategory').value = video.category;
            document.getElementById('videoUrl').value = video.video_url || '';
            document.getElementById('thumbnailUrl').value = video.thumbnail_url || '';
            document.getElementById('videoDuration').value = video.duration || '';
            document.getElementById('videoViews').value = video.views || '0';
            document.getElementById('releaseDate').value = video.release_date || '';
            
            document.getElementById('videoModal').classList.add('active');
        }

        // Close modal when clicking outside
        document.getElementById('videoModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>
