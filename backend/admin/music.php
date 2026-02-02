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
            case 'add_album':
                $title = $_POST['title'] ?? '';
                $year = $_POST['year'] ?? date('Y');
                $description = $_POST['description'] ?? '';
                $cover_image = $_POST['cover_image'] ?? '';
                
                $stmt = $connection->prepare("INSERT INTO albums (title, year, description, cover_image) VALUES (?, ?, ?, ?)");
                $stmt->execute([$title, $year, $description, $cover_image]);
                $album_id = $connection->lastInsertId();
                
                // Add tracks if provided
                if (isset($_POST['tracks']) && is_array($_POST['tracks'])) {
                    foreach ($_POST['tracks'] as $track) {
                        if (!empty($track['title'])) {
                            $track_stmt = $connection->prepare("INSERT INTO tracks (album_id, title, duration, track_number) VALUES (?, ?, ?, ?)");
                            $track_stmt->execute([$album_id, $track['title'], $track['duration'] ?? '', $track['number'] ?? 1]);
                        }
                    }
                }
                
                $_SESSION['success'] = "Album added successfully!";
                break;
                
            case 'add_single':
                $title = $_POST['title'] ?? '';
                $artist = $_POST['artist'] ?? '';
                $duration = $_POST['duration'] ?? '';
                $release_date = $_POST['release_date'] ?? date('Y-m-d');
                $cover_image = $_POST['cover_image'] ?? '';
                
                $stmt = $connection->prepare("INSERT INTO singles (title, artist, duration, release_date, cover_image) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$title, $artist, $duration, $release_date, $cover_image]);
                
                $_SESSION['success'] = "Single added successfully!";
                break;
                
            case 'delete_album':
                $id = $_POST['id'] ?? 0;
                
                // Delete tracks first
                $connection->prepare("DELETE FROM tracks WHERE album_id = ?")->execute([$id]);
                // Delete album
                $connection->prepare("DELETE FROM albums WHERE id = ?")->execute([$id]);
                
                $_SESSION['success'] = "Album deleted successfully!";
                break;
                
            case 'delete_single':
                $id = $_POST['id'] ?? 0;
                $connection->prepare("DELETE FROM singles WHERE id = ?")->execute([$id]);
                $_SESSION['success'] = "Single deleted successfully!";
                break;
        }
        
        header('Location: music.php');
        exit();
    }
}

// Get data
$albums = $connection->query("SELECT * FROM albums ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$singles = $connection->query("SELECT * FROM singles ORDER BY release_date DESC")->fetchAll(PDO::FETCH_ASSOC);

// Get tracks for each album
foreach ($albums as &$album) {
    $tracks_stmt = $connection->prepare("SELECT * FROM tracks WHERE album_id = ? ORDER BY track_number");
    $tracks_stmt->execute([$album['id']]);
    $album['tracks'] = $tracks_stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Management - Admin Dashboard</title>
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
        }

        .tab.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Cards */
        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }

        .card-title {
            color: #333;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .card-meta {
            color: #666;
            font-size: 0.9rem;
            margin: 0.5rem 0;
        }

        .card-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        /* Forms */
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

        .form-input, .form-textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: border-color 0.3s ease;
        }

        .form-input:focus, .form-textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .track-list {
            margin-top: 1rem;
        }

        .track-item {
            display: flex;
            gap: 1rem;
            margin-bottom: 0.5rem;
            align-items: center;
        }

        .track-item input {
            flex: 1;
        }

        .track-item input[type="number"] {
            width: 80px;
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

            .content-grid {
                grid-template-columns: 1fr;
            }

            .tabs {
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
                <a href="music.php" class="nav-item active">
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
                <h1>Music Management</h1>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="openAlbumModal()">
                        <i class="fas fa-plus"></i> Add Album
                    </button>
                    <button class="btn btn-primary" onclick="openSingleModal()">
                        <i class="fas fa-plus"></i> Add Single
                    </button>
                </div>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <!-- Tabs -->
            <div class="tabs">
                <button class="tab active" onclick="switchTab('albums')">Albums</button>
                <button class="tab" onclick="switchTab('singles')">Singles</button>
            </div>

            <!-- Albums Tab -->
            <div id="albums-tab" class="tab-content active">
                <div class="content-grid">
                    <?php foreach ($albums as $album): ?>
                        <div class="card">
                            <div class="card-header">
                                <div>
                                    <h3 class="card-title"><?= htmlspecialchars($album['title']) ?></h3>
                                    <div class="card-meta">
                                        <i class="fas fa-calendar"></i> <?= htmlspecialchars($album['year']) ?>
                                        <?php if ($album['cover_image']): ?>
                                            <br><i class="fas fa-image"></i> Cover image set
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card-actions">
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete_album">
                                        <input type="hidden" name="id" value="<?= $album['id'] ?>">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <?php if ($album['description']): ?>
                                <p style="color: #666; margin: 1rem 0;"><?= htmlspecialchars($album['description']) ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($album['tracks'])): ?>
                                <div style="margin-top: 1rem;">
                                    <h4 style="color: #333; margin-bottom: 0.5rem;">Tracks:</h4>
                                    <?php foreach ($album['tracks'] as $track): ?>
                                        <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #eee;">
                                            <span><?= $track['track_number'] ?>. <?= htmlspecialchars($track['title']) ?></span>
                                            <span style="color: #666;"><?= htmlspecialchars($track['duration']) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php if (empty($albums)): ?>
                        <div class="card">
                            <h3 style="color: #333; text-align: center; padding: 2rem;">
                                <i class="fas fa-compact-disc" style="font-size: 3rem; color: #ddd; margin-bottom: 1rem; display: block;"></i>
                                No albums yet
                            </h3>
                            <p style="text-align: center; color: #666;">Start by adding your first album</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Singles Tab -->
            <div id="singles-tab" class="tab-content">
                <div class="content-grid">
                    <?php foreach ($singles as $single): ?>
                        <div class="card">
                            <div class="card-header">
                                <div>
                                    <h3 class="card-title"><?= htmlspecialchars($single['title']) ?></h3>
                                    <div class="card-meta">
                                        <i class="fas fa-user"></i> <?= htmlspecialchars($single['artist']) ?>
                                        <br><i class="fas fa-clock"></i> <?= htmlspecialchars($single['duration']) ?>
                                        <br><i class="fas fa-calendar"></i> <?= date('M j, Y', strtotime($single['release_date'])) ?>
                                    </div>
                                </div>
                                <div class="card-actions">
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete_single">
                                        <input type="hidden" name="id" value="<?= $single['id'] ?>">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php if (empty($singles)): ?>
                        <div class="card">
                            <h3 style="color: #333; text-align: center; padding: 2rem;">
                                <i class="fas fa-music" style="font-size: 3rem; color: #ddd; margin-bottom: 1rem; display: block;"></i>
                                No singles yet
                            </h3>
                            <p style="text-align: center; color: #666;">Start by adding your first single</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Album Modal -->
    <div id="albumModal" class="modal">
        <div class="modal-content">
            <h2 style="margin-bottom: 1.5rem;">Add Album</h2>
            <form method="POST">
                <input type="hidden" name="action" value="add_album">
                
                <div class="form-group">
                    <label class="form-label">Album Title *</label>
                    <input type="text" name="title" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Year</label>
                    <input type="number" name="year" class="form-input" value="<?= date('Y') ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-textarea"></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Cover Image URL</label>
                    <input type="url" name="cover_image" class="form-input" placeholder="https://example.com/cover.jpg">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Tracks</label>
                    <div id="tracksList" class="track-list">
                        <div class="track-item">
                            <input type="number" name="tracks[0][number]" placeholder="#" min="1" value="1">
                            <input type="text" name="tracks[0][title]" placeholder="Track title">
                            <input type="text" name="tracks[0][duration]" placeholder="3:45">
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary" onclick="addTrack()">
                        <i class="fas fa-plus"></i> Add Track
                    </button>
                </div>
                
                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeAlbumModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Album</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Single Modal -->
    <div id="singleModal" class="modal">
        <div class="modal-content">
            <h2 style="margin-bottom: 1.5rem;">Add Single</h2>
            <form method="POST">
                <input type="hidden" name="action" value="add_single">
                
                <div class="form-group">
                    <label class="form-label">Single Title *</label>
                    <input type="text" name="title" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Artist</label>
                    <input type="text" name="artist" class="form-input" value="Artist Name">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Duration</label>
                    <input type="text" name="duration" class="form-input" placeholder="3:45">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Release Date</label>
                    <input type="date" name="release_date" class="form-input" value="<?= date('Y-m-d') ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Cover Image URL</label>
                    <input type="url" name="cover_image" class="form-input" placeholder="https://example.com/cover.jpg">
                </div>
                
                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeSingleModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Single</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let trackCount = 1;

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

        function openAlbumModal() {
            document.getElementById('albumModal').classList.add('active');
        }

        function closeAlbumModal() {
            document.getElementById('albumModal').classList.remove('active');
        }

        function openSingleModal() {
            document.getElementById('singleModal').classList.add('active');
        }

        function closeSingleModal() {
            document.getElementById('singleModal').classList.remove('active');
        }

        function addTrack() {
            const tracksList = document.getElementById('tracksList');
            const trackItem = document.createElement('div');
            trackItem.className = 'track-item';
            trackItem.innerHTML = `
                <input type="number" name="tracks[${trackCount}][number]" placeholder="#" min="1" value="${trackCount + 1}">
                <input type="text" name="tracks[${trackCount}][title]" placeholder="Track title">
                <input type="text" name="tracks[${trackCount}][duration]" placeholder="3:45">
                <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            tracksList.appendChild(trackItem);
            trackCount++;
        }

        // Close modals when clicking outside
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>
