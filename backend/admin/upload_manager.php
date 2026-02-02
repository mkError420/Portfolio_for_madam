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

// Handle file uploads
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'upload_image':
                handleImageUpload();
                break;
            case 'upload_audio':
                handleAudioUpload();
                break;
            case 'upload_video_thumbnail':
                handleVideoThumbnailUpload();
                break;
            case 'bulk_upload_gallery':
                handleBulkGalleryUpload();
                break;
        }
    }
}

function handleImageUpload() {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $upload_dir = '../uploads/images/';
        $category = $_POST['category'] ?? 'general';
        
        // Create category directory
        $category_dir = $upload_dir . $category . '/';
        if (!file_exists($category_dir)) {
            mkdir($category_dir, 0777, true);
        }
        
        // Generate unique filename
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            $new_filename = uniqid() . '.' . $file_extension;
            $target_file = $category_dir . $new_filename;
            
            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                $relative_path = 'uploads/images/' . $category . '/' . $new_filename;
                echo json_encode([
                    'success' => true,
                    'filename' => $new_filename,
                    'path' => $relative_path,
                    'size' => $file['size'],
                    'type' => $file['type']
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to move uploaded file']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid file type']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'No file uploaded']);
    }
    exit;
}

function handleAudioUpload() {
    if (isset($_FILES['audio']) && $_FILES['audio']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['audio'];
        $upload_dir = '../uploads/audio/';
        
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['mp3', 'wav', 'ogg', 'm4a', 'flac'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            $new_filename = uniqid() . '.' . $file_extension;
            $target_file = $upload_dir . $new_filename;
            
            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                $relative_path = 'uploads/audio/' . $new_filename;
                echo json_encode([
                    'success' => true,
                    'filename' => $new_filename,
                    'path' => $relative_path,
                    'size' => $file['size'],
                    'type' => $file['type']
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to move uploaded file']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid file type']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'No file uploaded']);
    }
    exit;
}

function handleVideoThumbnailUpload() {
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['thumbnail'];
        $upload_dir = '../uploads/thumbnails/';
        
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            $new_filename = uniqid() . '.' . $file_extension;
            $target_file = $upload_dir . $new_filename;
            
            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                $relative_path = 'uploads/thumbnails/' . $new_filename;
                echo json_encode([
                    'success' => true,
                    'filename' => $new_filename,
                    'path' => $relative_path,
                    'size' => $file['size'],
                    'type' => $file['type']
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to move uploaded file']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid file type']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'No file uploaded']);
    }
    exit;
}

function handleBulkGalleryUpload() {
    $uploaded_files = [];
    $errors = [];
    
    if (isset($_FILES['images'])) {
        $files = $_FILES['images'];
        $upload_dir = '../uploads/gallery/';
        $category = $_POST['category'] ?? 'general';
        
        // Create category directory
        $category_dir = $upload_dir . $category . '/';
        if (!file_exists($category_dir)) {
            mkdir($category_dir, 0777, true);
        }
        
        foreach ($files['name'] as $key => $name) {
            if ($files['error'][$key] === UPLOAD_ERR_OK) {
                $file_extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if (in_array($file_extension, $allowed_extensions)) {
                    $new_filename = uniqid() . '.' . $file_extension;
                    $target_file = $category_dir . $new_filename;
                    
                    if (move_uploaded_file($files['tmp_name'][$key], $target_file)) {
                        $relative_path = 'uploads/gallery/' . $category . '/' . $new_filename;
                        $uploaded_files[] = [
                            'original_name' => $name,
                            'filename' => $new_filename,
                            'path' => $relative_path,
                            'size' => $files['size'][$key],
                            'type' => $files['type'][$key]
                        ];
                    } else {
                        $errors[] = 'Failed to upload: ' . $name;
                    }
                } else {
                    $errors[] = 'Invalid file type: ' . $name;
                }
            } else {
                $errors[] = 'Upload error for: ' . $name;
            }
        }
    }
    
    echo json_encode([
        'success' => count($uploaded_files) > 0,
        'uploaded_files' => $uploaded_files,
        'errors' => $errors
    ]);
    exit;
}

// Get upload statistics
$upload_stats = [
    'images' => count(glob('../uploads/images/*/*')),
    'audio' => count(glob('../uploads/audio/*')),
    'thumbnails' => count(glob('../uploads/thumbnails/*')),
    'gallery' => count(glob('../uploads/gallery/*/*'))
];

// Get recent uploads
$recent_uploads = [];
$upload_dirs = ['../uploads/images/', '../uploads/audio/', '../uploads/thumbnails/', '../uploads/gallery/'];

foreach ($upload_dirs as $dir) {
    if (file_exists($dir)) {
        $files = glob($dir . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                $recent_uploads[] = [
                    'name' => basename($file),
                    'path' => str_replace('../', '', $file),
                    'size' => filesize($file),
                    'modified' => filemtime($file),
                    'type' => pathinfo($file, PATHINFO_EXTENSION)
                ];
            }
        }
    }
}

// Sort by modification time
usort($recent_uploads, function($a, $b) {
    return $b['modified'] - $a['modified'];
});

$recent_uploads = array_slice($recent_uploads, 0, 20);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Manager - Admin Dashboard</title>
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

        .btn-success {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Upload Stats */
        .stats-grid {
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

        /* Upload Sections */
        .upload-sections {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
        }

        .upload-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .upload-card h2 {
            color: #333;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .upload-area {
            border: 2px dashed #ddd;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            transition: border-color 0.3s ease;
            cursor: pointer;
            margin-bottom: 1rem;
        }

        .upload-area:hover {
            border-color: #667eea;
        }

        .upload-area.dragover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.05);
        }

        .upload-icon {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }

        .form-select, .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.95rem;
        }

        .form-select:focus, .form-input:focus {
            outline: none;
            border-color: #667eea;
        }

        /* Recent Uploads */
        .recent-uploads {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .recent-uploads h2 {
            color: #333;
            margin-bottom: 1.5rem;
        }

        .upload-list {
            display: grid;
            gap: 1rem;
        }

        .upload-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 8px;
            transition: transform 0.3s ease;
        }

        .upload-item:hover {
            transform: translateY(-2px);
        }

        .upload-info {
            flex: 1;
        }

        .upload-name {
            font-weight: 500;
            color: #333;
            margin-bottom: 0.25rem;
        }

        .upload-meta {
            font-size: 0.85rem;
            color: #666;
        }

        .upload-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-small {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }

        /* Progress Bar */
        .progress-bar {
            width: 100%;
            height: 6px;
            background: #e0e0e0;
            border-radius: 3px;
            overflow: hidden;
            margin-top: 1rem;
            display: none;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            width: 0%;
            transition: width 0.3s ease;
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

            .upload-sections {
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
                <a href="upload_manager.php" class="nav-item active">
                    <i class="fas fa-upload"></i> Upload Manager
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
                <h1>Upload Manager</h1>
                <div class="header-actions">
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>

            <!-- Upload Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?= $upload_stats['images'] ?></div>
                    <div class="stat-label">Images</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= $upload_stats['audio'] ?></div>
                    <div class="stat-label">Audio Files</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= $upload_stats['thumbnails'] ?></div>
                    <div class="stat-label">Thumbnails</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= $upload_stats['gallery'] ?></div>
                    <div class="stat-label">Gallery Images</div>
                </div>
            </div>

            <!-- Upload Sections -->
            <div class="upload-sections">
                <!-- Image Upload -->
                <div class="upload-card">
                    <h2><i class="fas fa-image"></i> Image Upload</h2>
                    <form id="imageUploadForm" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select">
                                <option value="general">General</option>
                                <option value="album-covers">Album Covers</option>
                                <option value="artist">Artist Photos</option>
                                <option value="performance">Performance</option>
                                <option value="behind-scenes">Behind Scenes</option>
                            </select>
                        </div>
                        <div class="upload-area" id="imageDropZone">
                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                            <p>Click to upload or drag and drop</p>
                            <p style="font-size: 0.85rem; color: #666;">JPG, PNG, GIF, WebP (Max 10MB)</p>
                            <input type="file" id="imageFile" name="image" accept="image/*" style="display: none;">
                        </div>
                        <div class="progress-bar" id="imageProgress">
                            <div class="progress-fill" id="imageProgressFill"></div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Upload Image
                        </button>
                    </form>
                </div>

                <!-- Audio Upload -->
                <div class="upload-card">
                    <h2><i class="fas fa-music"></i> Audio Upload</h2>
                    <form id="audioUploadForm" enctype="multipart/form-data">
                        <div class="upload-area" id="audioDropZone">
                            <i class="fas fa-file-audio upload-icon"></i>
                            <p>Click to upload or drag and drop</p>
                            <p style="font-size: 0.85rem; color: #666;">MP3, WAV, OGG, M4A, FLAC (Max 50MB)</p>
                            <input type="file" id="audioFile" name="audio" accept="audio/*" style="display: none;">
                        </div>
                        <div class="progress-bar" id="audioProgress">
                            <div class="progress-fill" id="audioProgressFill"></div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Upload Audio
                        </button>
                    </form>
                </div>

                <!-- Video Thumbnail Upload -->
                <div class="upload-card">
                    <h2><i class="fas fa-video"></i> Video Thumbnail</h2>
                    <form id="thumbnailUploadForm" enctype="multipart/form-data">
                        <div class="upload-area" id="thumbnailDropZone">
                            <i class="fas fa-image upload-icon"></i>
                            <p>Click to upload or drag and drop</p>
                            <p style="font-size: 0.85rem; color: #666;">JPG, PNG, GIF, WebP (Max 5MB)</p>
                            <input type="file" id="thumbnailFile" name="thumbnail" accept="image/*" style="display: none;">
                        </div>
                        <div class="progress-bar" id="thumbnailProgress">
                            <div class="progress-fill" id="thumbnailProgressFill"></div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Upload Thumbnail
                        </button>
                    </form>
                </div>

                <!-- Bulk Gallery Upload -->
                <div class="upload-card">
                    <h2><i class="fas fa-images"></i> Bulk Gallery Upload</h2>
                    <form id="bulkUploadForm" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select">
                                <option value="performance">Performance</option>
                                <option value="studio">Studio</option>
                                <option value="behind">Behind Scenes</option>
                                <option value="general">General</option>
                            </select>
                        </div>
                        <div class="upload-area" id="bulkDropZone">
                            <i class="fas fa-images upload-icon"></i>
                            <p>Click to upload or drag and drop multiple images</p>
                            <p style="font-size: 0.85rem; color: #666;">JPG, PNG, GIF, WebP (Max 10MB each)</p>
                            <input type="file" id="bulkFiles" name="images[]" accept="image/*" multiple style="display: none;">
                        </div>
                        <div class="progress-bar" id="bulkProgress">
                            <div class="progress-fill" id="bulkProgressFill"></div>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-upload"></i> Upload Multiple Images
                        </button>
                    </form>
                </div>
            </div>

            <!-- Recent Uploads -->
            <div class="recent-uploads">
                <h2>Recent Uploads</h2>
                <div class="upload-list">
                    <?php if (empty($recent_uploads)): ?>
                        <div style="text-align: center; padding: 2rem; color: #666;">
                            <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                            <p>No uploads yet</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($recent_uploads as $upload): ?>
                            <div class="upload-item">
                                <div class="upload-info">
                                    <div class="upload-name"><?= htmlspecialchars($upload['name']) ?></div>
                                    <div class="upload-meta">
                                        <?= strtoupper($upload['type']) ?> • <?= number_format($upload['size'] / 1024, 2) ?> KB • <?= date('M j, Y g:i A', $upload['modified']) ?>
                                    </div>
                                </div>
                                <div class="upload-actions">
                                    <button class="btn btn-small btn-secondary" onclick="copyPath('<?= htmlspecialchars($upload['path']) ?>')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    <button class="btn btn-small btn-secondary" onclick="viewFile('<?= htmlspecialchars($upload['path']) ?>')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Image Upload
        const imageDropZone = document.getElementById('imageDropZone');
        const imageFile = document.getElementById('imageFile');
        const imageProgress = document.getElementById('imageProgress');
        const imageProgressFill = document.getElementById('imageProgressFill');

        imageDropZone.addEventListener('click', () => imageFile.click());
        
        imageDropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            imageDropZone.classList.add('dragover');
        });
        
        imageDropZone.addEventListener('dragleave', () => {
            imageDropZone.classList.remove('dragover');
        });
        
        imageDropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            imageDropZone.classList.remove('dragover');
            imageFile.files = e.dataTransfer.files;
        });

        document.getElementById('imageUploadForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            await uploadFile('image', 'imageUploadForm', 'imageProgress', 'imageProgressFill');
        });

        // Audio Upload
        const audioDropZone = document.getElementById('audioDropZone');
        const audioFile = document.getElementById('audioFile');
        const audioProgress = document.getElementById('audioProgress');
        const audioProgressFill = document.getElementById('audioProgressFill');

        audioDropZone.addEventListener('click', () => audioFile.click());
        
        audioDropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            audioDropZone.classList.add('dragover');
        });
        
        audioDropZone.addEventListener('dragleave', () => {
            audioDropZone.classList.remove('dragover');
        });
        
        audioDropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            audioDropZone.classList.remove('dragover');
            audioFile.files = e.dataTransfer.files;
        });

        document.getElementById('audioUploadForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            await uploadFile('audio', 'audioUploadForm', 'audioProgress', 'audioProgressFill');
        });

        // Thumbnail Upload
        const thumbnailDropZone = document.getElementById('thumbnailDropZone');
        const thumbnailFile = document.getElementById('thumbnailFile');
        const thumbnailProgress = document.getElementById('thumbnailProgress');
        const thumbnailProgressFill = document.getElementById('thumbnailProgressFill');

        thumbnailDropZone.addEventListener('click', () => thumbnailFile.click());
        
        thumbnailDropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            thumbnailDropZone.classList.add('dragover');
        });
        
        thumbnailDropZone.addEventListener('dragleave', () => {
            thumbnailDropZone.classList.remove('dragover');
        });
        
        thumbnailDropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            thumbnailDropZone.classList.remove('dragover');
            thumbnailFile.files = e.dataTransfer.files;
        });

        document.getElementById('thumbnailUploadForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            await uploadFile('thumbnail', 'thumbnailUploadForm', 'thumbnailProgress', 'thumbnailProgressFill');
        });

        // Bulk Upload
        const bulkDropZone = document.getElementById('bulkDropZone');
        const bulkFiles = document.getElementById('bulkFiles');
        const bulkProgress = document.getElementById('bulkProgress');
        const bulkProgressFill = document.getElementById('bulkProgressFill');

        bulkDropZone.addEventListener('click', () => bulkFiles.click());
        
        bulkDropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            bulkDropZone.classList.add('dragover');
        });
        
        bulkDropZone.addEventListener('dragleave', () => {
            bulkDropZone.classList.remove('dragover');
        });
        
        bulkDropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            bulkDropZone.classList.remove('dragover');
            bulkFiles.files = e.dataTransfer.files;
        });

        document.getElementById('bulkUploadForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            await uploadBulkFiles();
        });

        async function uploadFile(type, formId, progressId, progressFillId) {
            const formData = new FormData(document.getElementById(formId));
            const progressBar = document.getElementById(progressId);
            const progressFill = document.getElementById(progressFillId);
            
            progressBar.style.display = 'block';
            
            try {
                const response = await fetch('upload_manager.php?action=upload_' + type, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    progressFill.style.width = '100%';
                    setTimeout(() => {
                        progressBar.style.display = 'none';
                        progressFill.style.width = '0%';
                        alert('Upload successful!');
                        location.reload();
                    }, 1000);
                } else {
                    alert('Upload failed: ' + result.error);
                    progressBar.style.display = 'none';
                    progressFill.style.width = '0%';
                }
            } catch (error) {
                alert('Upload error: ' + error.message);
                progressBar.style.display = 'none';
                progressFill.style.width = '0%';
            }
        }

        async function uploadBulkFiles() {
            const formData = new FormData(document.getElementById('bulkUploadForm'));
            const progressBar = document.getElementById('bulkProgress');
            const progressFill = document.getElementById('bulkProgressFill');
            
            progressBar.style.display = 'block';
            
            try {
                const response = await fetch('upload_manager.php?action=bulk_upload_gallery', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    progressFill.style.width = '100%';
                    setTimeout(() => {
                        progressBar.style.display = 'none';
                        progressFill.style.width = '0%';
                        alert(`Successfully uploaded ${result.uploaded_files.length} files!`);
                        if (result.errors.length > 0) {
                            alert('Errors: ' + result.errors.join(', '));
                        }
                        location.reload();
                    }, 1000);
                } else {
                    alert('Upload failed: ' + result.error);
                    progressBar.style.display = 'none';
                    progressFill.style.width = '0%';
                }
            } catch (error) {
                alert('Upload error: ' + error.message);
                progressBar.style.display = 'none';
                progressFill.style.width = '0%';
            }
        }

        function copyPath(path) {
            navigator.clipboard.writeText(path).then(() => {
                alert('Path copied to clipboard!');
            });
        }

        function viewFile(path) {
            window.open('../' + path, '_blank');
        }
    </script>
</body>
</html>
