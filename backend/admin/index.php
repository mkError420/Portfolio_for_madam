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
    <title>Admin Panel - Singer Portfolio</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #1a1a1a;
            color: #ffffff;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background: #2a2a2a;
            padding: 1rem 0;
            margin-bottom: 2rem;
            border-bottom: 3px solid #ff6b6b;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        h1 {
            color: #ffffff;
            font-size: 1.8rem;
        }
        
        .logout-btn {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        
        .logout-btn:hover {
            background: #ff5252;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: #2a2a2a;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            border: 1px solid #444;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #ff6b6b;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #cccccc;
        }
        
        .forms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
        }
        
        .form-section {
            background: #2a2a2a;
            padding: 2rem;
            border-radius: 10px;
            border: 1px solid #444;
        }
        
        .form-section h2 {
            color: #ffffff;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #ff6b6b;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #cccccc;
            font-weight: 500;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 0.8rem;
            background: #1a1a1a;
            border: 1px solid #444;
            border-radius: 5px;
            color: #ffffff;
            font-size: 1rem;
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #ff6b6b;
        }
        
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .btn {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s ease;
        }
        
        .btn:hover {
            background: #ff5252;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background: #4CAF50;
            color: white;
        }
        
        .alert-error {
            background: #f44336;
            color: white;
        }
        
        .nav-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid #444;
        }
        
        .nav-tab {
            background: none;
            border: none;
            color: #cccccc;
            padding: 1rem 1.5rem;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
        }
        
        .nav-tab.active {
            color: #ff6b6b;
            border-bottom-color: #ff6b6b;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <h1>🎵 Admin Panel - Singer Portfolio</h1>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Statistics Dashboard -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['albums']; ?></div>
                <div class="stat-label">Albums</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['videos']; ?></div>
                <div class="stat-label">Videos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['gallery']; ?></div>
                <div class="stat-label">Gallery Images</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['tour_dates']; ?></div>
                <div class="stat-label">Upcoming Shows</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['messages']; ?></div>
                <div class="stat-label">New Messages</div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="nav-tabs">
            <button class="nav-tab active" onclick="showTab('albums')">Add Album</button>
            <button class="nav-tab" onclick="showTab('tour')">Add Tour Date</button>
            <button class="nav-tab" onclick="showTab('videos')">Add Video</button>
            <button class="nav-tab" onclick="showTab('gallery')">Add Gallery Image</button>
        </div>

        <!-- Add Album Form -->
        <div id="albums" class="tab-content active">
            <div class="form-section">
                <h2>Add New Album</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="add_album">
                    
                    <div class="form-group">
                        <label for="album_title">Album Title</label>
                        <input type="text" id="album_title" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="album_year">Year</label>
                        <input type="number" id="album_year" name="year" min="1900" max="2030" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="album_description">Description</label>
                        <textarea id="album_description" name="description"></textarea>
                    </div>
                    
                    <button type="submit" class="btn">Add Album</button>
                </form>
            </div>
        </div>

        <!-- Add Tour Date Form -->
        <div id="tour" class="tab-content">
            <div class="form-section">
                <h2>Add Tour Date</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="add_tour_date">
                    
                    <div class="form-group">
                        <label for="tour_date">Date</label>
                        <input type="date" id="tour_date" name="date" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="tour_venue">Venue</label>
                        <input type="text" id="tour_venue" name="venue" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="tour_city">City</label>
                        <input type="text" id="tour_city" name="city" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="tour_country">Country</label>
                        <input type="text" id="tour_country" name="country" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="tour_ticket_url">Ticket URL</label>
                        <input type="url" id="tour_ticket_url" name="ticket_url">
                    </div>
                    
                    <div class="form-group">
                        <label for="tour_price">Price Range</label>
                        <input type="text" id="tour_price" name="price_range" placeholder="$50 - $200">
                    </div>
                    
                    <button type="submit" class="btn">Add Tour Date</button>
                </form>
            </div>
        </div>

        <!-- Add Video Form -->
        <div id="videos" class="tab-content">
            <div class="form-section">
                <h2>Add Video</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="add_video">
                    
                    <div class="form-group">
                        <label for="video_title">Title</label>
                        <input type="text" id="video_title" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="video_description">Description</label>
                        <textarea id="video_description" name="description"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="video_id">YouTube Video ID</label>
                        <input type="text" id="video_id" name="video_id" required placeholder="dQw4w9WgXcQ">
                    </div>
                    
                    <div class="form-group">
                        <label for="video_category">Category</label>
                        <select id="video_category" name="category" required>
                            <option value="music_video">Music Video</option>
                            <option value="live_performance">Live Performance</option>
                            <option value="behind_scenes">Behind Scenes</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="video_duration">Duration</label>
                        <input type="text" id="video_duration" name="duration" placeholder="3:45">
                    </div>
                    
                    <div class="form-group">
                        <label for="video_venue">Venue (for live performances)</label>
                        <input type="text" id="video_venue" name="venue">
                    </div>
                    
                    <button type="submit" class="btn">Add Video</button>
                </form>
            </div>
        </div>

        <!-- Add Gallery Image Form -->
        <div id="gallery" class="tab-content">
            <div class="form-section">
                <h2>Add Gallery Image</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="add_gallery_image">
                    
                    <div class="form-group">
                        <label for="gallery_title">Title</label>
                        <input type="text" id="gallery_title" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="gallery_description">Description</label>
                        <textarea id="gallery_description" name="description"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="gallery_image_url">Image URL</label>
                        <input type="url" id="gallery_image_url" name="image_url" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="gallery_category">Category</label>
                        <select id="gallery_category" name="category" required>
                            <option value="performance">Performance</option>
                            <option value="studio">Studio</option>
                            <option value="behind_scenes">Behind Scenes</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn">Add Image</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Remove active class from all tabs
            const tabs = document.querySelectorAll('.nav-tab');
            tabs.forEach(tab => tab.classList.remove('active'));
            
            // Show selected tab content
            document.getElementById(tabName).classList.add('active');
            
            // Add active class to clicked tab
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
