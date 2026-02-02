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

// Handle file upload
function handleFileUpload($file, $target_dir) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Generate unique filename
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array($file_extension, $allowed_extensions)) {
        return null;
    }

    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . '/' . $new_filename;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return $new_filename;
    }

    return null;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_image':
                $title = $_POST['title'] ?? '';
                $description = $_POST['description'] ?? '';
                $category = $_POST['category'] ?? 'performance';
                
                // Handle image upload
                $image_filename = null;
                $thumbnail_filename = null;
                
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $image_filename = handleFileUpload($_FILES['image'], '../uploads/gallery');
                }
                
                if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                    $thumbnail_filename = handleFileUpload($_FILES['thumbnail'], '../uploads/gallery/thumbnails');
                }
                
                // If no files uploaded, use URLs
                $image_url = $_POST['image_url'] ?? '';
                $thumbnail_url = $_POST['thumbnail_url'] ?? '';
                
                if ($image_filename) {
                    $image_url = '../uploads/gallery/' . $image_filename;
                }
                
                if ($thumbnail_filename) {
                    $thumbnail_url = '../uploads/gallery/thumbnails/' . $thumbnail_filename;
                }
                
                if (empty($image_url) && empty($thumbnail_url)) {
                    $_SESSION['error'] = "Please provide either an image file or URL";
                } else {
                    $stmt = $connection->prepare("INSERT INTO gallery (title, description, image_url, thumbnail_url, category) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$title, $description, $image_url, $thumbnail_url, $category]);
                    $_SESSION['success'] = "Image added successfully!";
                }
                break;
                
            case 'delete_image':
                $id = $_POST['id'] ?? 0;
                
                // Get image info before deletion
                $stmt = $connection->prepare("SELECT image_url, thumbnail_url FROM gallery WHERE id = ?");
                $stmt->execute([$id]);
                $image_info = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Delete files if they exist
                if ($image_info) {
                    if ($image_info['image_url'] && file_exists($image_info['image_url'])) {
                        unlink($image_info['image_url']);
                    }
                    if ($image_info['thumbnail_url'] && file_exists($image_info['thumbnail_url'])) {
                        unlink($image_info['thumbnail_url']);
                    }
                }
                
                // Delete from database
                $connection->prepare("DELETE FROM gallery WHERE id = ?")->execute([$id]);
                $_SESSION['success'] = "Image deleted successfully!";
                break;
        }
        
        header('Location: gallery.php');
        exit();
    }
}

// Get gallery images
$gallery_images = $connection->query("SELECT * FROM gallery ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery Management - Admin Dashboard</title>
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

        /* Gallery Grid */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .gallery-item {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .gallery-item:hover {
            transform: translateY(-5px);
        }

        .gallery-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: linear-gradient(45deg, #f0f0f0 25%, transparent 25%, transparent 75%, #f0f0f0 75%, #f0f0f0),
                        linear-gradient(45deg, #f0f0f0 25%, transparent 25%, transparent 75%, #f0f0f0 75%, #f0f0f0);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
        }

        .gallery-info {
            padding: 1.5rem;
        }

        .gallery-title {
            color: #333;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .gallery-category {
            display: inline-block;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
        }

        .gallery-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .gallery-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .gallery-date {
            color: #999;
            font-size: 0.8rem;
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

        .file-upload {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            transition: border-color 0.3s ease;
        }

        .file-upload:hover {
            border-color: #667eea;
        }

        .file-upload input[type="file"] {
            display: none;
        }

        .file-upload-label {
            cursor: pointer;
            color: #666;
        }

        .file-upload-label i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            display: block;
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

            .gallery-grid {
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
                <a href="videos.php" class="nav-item">
                    <i class="fas fa-video"></i> Videos
                </a>
                <a href="gallery.php" class="nav-item active">
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
                <h1>Gallery Management</h1>
                <button class="btn btn-primary" onclick="openModal()">
                    <i class="fas fa-plus"></i> Add Image
                </button>
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

            <!-- Gallery Grid -->
            <div class="gallery-grid">
                <?php foreach ($gallery_images as $image): ?>
                    <div class="gallery-item">
                        <?php if ($image['thumbnail_url']): ?>
                            <img src="<?= htmlspecialchars($image['thumbnail_url']) ?>" alt="<?= htmlspecialchars($image['title']) ?>" class="gallery-image">
                        <?php elseif ($image['image_url']): ?>
                            <img src="<?= htmlspecialchars($image['image_url']) ?>" alt="<?= htmlspecialchars($image['title']) ?>" class="gallery-image">
                        <?php else: ?>
                            <div class="gallery-image"></div>
                        <?php endif; ?>
                        
                        <div class="gallery-info">
                            <h3 class="gallery-title"><?= htmlspecialchars($image['title']) ?></h3>
                            <span class="gallery-category"><?= htmlspecialchars(ucfirst($image['category'])) ?></span>
                            <?php if ($image['description']): ?>
                                <p class="gallery-description"><?= htmlspecialchars($image['description']) ?></p>
                            <?php endif; ?>
                            <div class="gallery-actions">
                                <span class="gallery-date"><?= date('M j, Y', strtotime($image['created_at'])) ?></span>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="delete_image">
                                    <input type="hidden" name="id" value="<?= $image['id'] ?>">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <?php if (empty($gallery_images)): ?>
                    <div class="empty-state" style="grid-column: 1 / -1;">
                        <i class="fas fa-images"></i>
                        <h3>No gallery images yet</h3>
                        <p>Start by adding your first image to the gallery</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Add Image Modal -->
    <div id="addImageModal" class="modal">
        <div class="modal-content">
            <h2 style="margin-bottom: 1.5rem;">Add Gallery Image</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_image">
                
                <div class="form-group">
                    <label class="form-label">Title *</label>
                    <input type="text" name="title" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-textarea"></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select">
                        <option value="performance">Performance</option>
                        <option value="studio">Studio</option>
                        <option value="behind">Behind Scenes</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Image Upload</label>
                    <div class="file-upload">
                        <input type="file" name="image" id="imageFile" accept="image/*">
                        <label for="imageFile" class="file-upload-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <div>Click to upload image or drag and drop</div>
                            <small>PNG, JPG, GIF up to 10MB</small>
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Thumbnail Upload (optional)</label>
                    <div class="file-upload">
                        <input type="file" name="thumbnail" id="thumbnailFile" accept="image/*">
                        <label for="thumbnailFile" class="file-upload-label">
                            <i class="fas fa-image"></i>
                            <div>Click to upload thumbnail or drag and drop</div>
                            <small>PNG, JPG, GIF up to 10MB</small>
                        </label>
                    </div>
                </div>
                
                <div style="text-align: center; margin: 1rem 0; color: #666;">OR</div>
                
                <div class="form-group">
                    <label class="form-label">Image URL</label>
                    <input type="url" name="image_url" class="form-input" placeholder="https://example.com/image.jpg">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Thumbnail URL</label>
                    <input type="url" name="thumbnail_url" class="form-input" placeholder="https://example.com/thumbnail.jpg">
                </div>
                
                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Image</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('addImageModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('addImageModal').classList.remove('active');
        }

        // Close modal when clicking outside
        document.getElementById('addImageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // File upload preview
        document.getElementById('imageFile').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const label = this.nextElementSibling;
                label.innerHTML = `
                    <i class="fas fa-check-circle" style="color: #43e97b;"></i>
                    <div>${file.name}</div>
                    <small>Ready to upload</small>
                `;
            }
        });

        document.getElementById('thumbnailFile').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const label = this.nextElementSibling;
                label.innerHTML = `
                    <i class="fas fa-check-circle" style="color: #43e97b;"></i>
                    <div>${file.name}</div>
                    <small>Ready to upload</small>
                `;
            }
        });
    </script>
</body>
</html>
