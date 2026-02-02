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
            case 'add_tour_date':
                $venue = $_POST['venue'] ?? '';
                $city = $_POST['city'] ?? '';
                $country = $_POST['country'] ?? '';
                $date = $_POST['date'] ?? '';
                $time = $_POST['time'] ?? '';
                $ticket_url = $_POST['ticket_url'] ?? '';
                $price = $_POST['price'] ?? '';
                $description = $_POST['description'] ?? '';
                $status = $_POST['status'] ?? 'upcoming';
                
                $stmt = $connection->prepare("INSERT INTO tour_dates (venue, city, country, date, time, ticket_url, price, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$venue, $city, $country, $date, $time, $ticket_url, $price, $description, $status]);
                
                $_SESSION['success'] = "Tour date added successfully!";
                break;
                
            case 'delete_tour_date':
                $id = $_POST['id'] ?? 0;
                $connection->prepare("DELETE FROM tour_dates WHERE id = ?")->execute([$id]);
                $_SESSION['success'] = "Tour date deleted successfully!";
                break;
                
            case 'update_tour_date':
                $id = $_POST['id'] ?? 0;
                $venue = $_POST['venue'] ?? '';
                $city = $_POST['city'] ?? '';
                $country = $_POST['country'] ?? '';
                $date = $_POST['date'] ?? '';
                $time = $_POST['time'] ?? '';
                $ticket_url = $_POST['ticket_url'] ?? '';
                $price = $_POST['price'] ?? '';
                $description = $_POST['description'] ?? '';
                $status = $_POST['status'] ?? 'upcoming';
                
                $stmt = $connection->prepare("UPDATE tour_dates SET venue = ?, city = ?, country = ?, date = ?, time = ?, ticket_url = ?, price = ?, description = ?, status = ? WHERE id = ?");
                $stmt->execute([$venue, $city, $country, $date, $time, $ticket_url, $price, $description, $status, $id]);
                
                $_SESSION['success'] = "Tour date updated successfully!";
                break;
                
            case 'update_status':
                $id = $_POST['id'] ?? 0;
                $status = $_POST['status'] ?? 'upcoming';
                $connection->prepare("UPDATE tour_dates SET status = ? WHERE id = ?")->execute([$status, $id]);
                $_SESSION['success'] = "Status updated successfully!";
                break;
        }
        
        header('Location: tour.php');
        exit();
    }
}

// Get tour dates
$tour_dates = $connection->query("SELECT * FROM tour_dates ORDER BY date ASC")->fetchAll(PDO::FETCH_ASSOC);

// Separate upcoming and past dates
$upcoming_shows = [];
$past_shows = [];
$current_date = date('Y-m-d');

foreach ($tour_dates as $date) {
    if ($date['date'] >= $current_date) {
        $upcoming_shows[] = $date;
    } else {
        $past_shows[] = $date;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Management - Admin Dashboard</title>
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

        .btn-success {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, #feca57, #ff9ff3);
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

        /* Tour Cards */
        .tour-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }

        .tour-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            position: relative;
        }

        .tour-card:hover {
            transform: translateY(-5px);
        }

        .tour-status {
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-upcoming {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            color: white;
        }

        .status-soldout {
            background: linear-gradient(135deg, #ff6b6b, #ff4757);
            color: white;
        }

        .status-cancelled {
            background: linear-gradient(135deg, #636e72, #2d3436);
            color: white;
        }

        .tour-venue {
            color: #333;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .tour-location {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .tour-datetime {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            color: #666;
            font-size: 0.9rem;
        }

        .tour-datetime div {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .tour-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            line-height: 1.4;
        }

        .tour-actions {
            display: flex;
            gap: 0.5rem;
            justify-content: space-between;
            align-items: center;
        }

        .tour-price {
            font-size: 1.1rem;
            font-weight: 600;
            color: #667eea;
        }

        .tour-buttons {
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

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
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

            .tour-grid {
                grid-template-columns: 1fr;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .tour-actions {
                flex-direction: column;
                gap: 1rem;
            }

            .tour-buttons {
                justify-content: center;
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
                <a href="tour.php" class="nav-item active">
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
                <h1>Tour Management</h1>
                <button class="btn btn-primary" onclick="openModal()">
                    <i class="fas fa-plus"></i> Add Tour Date
                </button>
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
                <button class="tab active" onclick="switchTab('upcoming')">Upcoming Shows</button>
                <button class="tab" onclick="switchTab('past')">Past Shows</button>
                <button class="tab" onclick="switchTab('all')">All Shows</button>
            </div>

            <!-- Upcoming Shows Tab -->
            <div id="upcoming-tab" class="tab-content active">
                <div class="tour-grid">
                    <?php foreach ($upcoming_shows as $show): ?>
                        <div class="tour-card">
                            <span class="tour-status status-<?= htmlspecialchars($show['status']) ?>">
                                <?= htmlspecialchars(ucfirst($show['status'])) ?>
                            </span>
                            
                            <h3 class="tour-venue"><?= htmlspecialchars($show['venue']) ?></h3>
                            <div class="tour-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <?= htmlspecialchars($show['city']) ?>, <?= htmlspecialchars($show['country']) ?>
                            </div>
                            
                            <div class="tour-datetime">
                                <div>
                                    <i class="fas fa-calendar"></i>
                                    <?= date('M j, Y', strtotime($show['date'])) ?>
                                </div>
                                <?php if ($show['time']): ?>
                                    <div>
                                        <i class="fas fa-clock"></i>
                                        <?= htmlspecialchars($show['time']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($show['description']): ?>
                                <p class="tour-description"><?= htmlspecialchars($show['description']) ?></p>
                            <?php endif; ?>
                            
                            <div class="tour-actions">
                                <div class="tour-price">
                                    <?= $show['price'] ? '$' . htmlspecialchars($show['price']) : 'Free' ?>
                                </div>
                                <div class="tour-buttons">
                                    <button class="btn btn-secondary" onclick="editTourDate(<?= htmlspecialchars(json_encode($show)) ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete_tour_date">
                                        <input type="hidden" name="id" value="<?= $show['id'] ?>">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php if (empty($upcoming_shows)): ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-alt"></i>
                            <h3>No upcoming shows</h3>
                            <p>Add tour dates to see them here</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Past Shows Tab -->
            <div id="past-tab" class="tab-content">
                <div class="tour-grid">
                    <?php foreach ($past_shows as $show): ?>
                        <div class="tour-card">
                            <span class="tour-status status-<?= htmlspecialchars($show['status']) ?>">
                                <?= htmlspecialchars(ucfirst($show['status'])) ?>
                            </span>
                            
                            <h3 class="tour-venue"><?= htmlspecialchars($show['venue']) ?></h3>
                            <div class="tour-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <?= htmlspecialchars($show['city']) ?>, <?= htmlspecialchars($show['country']) ?>
                            </div>
                            
                            <div class="tour-datetime">
                                <div>
                                    <i class="fas fa-calendar"></i>
                                    <?= date('M j, Y', strtotime($show['date'])) ?>
                                </div>
                                <?php if ($show['time']): ?>
                                    <div>
                                        <i class="fas fa-clock"></i>
                                        <?= htmlspecialchars($show['time']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="tour-actions">
                                <div class="tour-price">
                                    <?= $show['price'] ? '$' . htmlspecialchars($show['price']) : 'Free' ?>
                                </div>
                                <div class="tour-buttons">
                                    <button class="btn btn-secondary" onclick="editTourDate(<?= htmlspecialchars(json_encode($show)) ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete_tour_date">
                                        <input type="hidden" name="id" value="<?= $show['id'] ?>">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php if (empty($past_shows)): ?>
                        <div class="empty-state">
                            <i class="fas fa-history"></i>
                            <h3>No past shows</h3>
                            <p>Past tour dates will appear here</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- All Shows Tab -->
            <div id="all-tab" class="tab-content">
                <div class="tour-grid">
                    <?php foreach ($tour_dates as $show): ?>
                        <div class="tour-card">
                            <span class="tour-status status-<?= htmlspecialchars($show['status']) ?>">
                                <?= htmlspecialchars(ucfirst($show['status'])) ?>
                            </span>
                            
                            <h3 class="tour-venue"><?= htmlspecialchars($show['venue']) ?></h3>
                            <div class="tour-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <?= htmlspecialchars($show['city']) ?>, <?= htmlspecialchars($show['country']) ?>
                            </div>
                            
                            <div class="tour-datetime">
                                <div>
                                    <i class="fas fa-calendar"></i>
                                    <?= date('M j, Y', strtotime($show['date'])) ?>
                                </div>
                                <?php if ($show['time']): ?>
                                    <div>
                                        <i class="fas fa-clock"></i>
                                        <?= htmlspecialchars($show['time']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($show['description']): ?>
                                <p class="tour-description"><?= htmlspecialchars($show['description']) ?></p>
                            <?php endif; ?>
                            
                            <div class="tour-actions">
                                <div class="tour-price">
                                    <?= $show['price'] ? '$' . htmlspecialchars($show['price']) : 'Free' ?>
                                </div>
                                <div class="tour-buttons">
                                    <button class="btn btn-secondary" onclick="editTourDate(<?= htmlspecialchars(json_encode($show)) ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="delete_tour_date">
                                        <input type="hidden" name="id" value="<?= $show['id'] ?>">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php if (empty($tour_dates)): ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-alt"></i>
                            <h3>No tour dates yet</h3>
                            <p>Start by adding your first tour date</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Add/Edit Tour Date Modal -->
    <div id="tourModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle" style="margin-bottom: 1.5rem;">Add Tour Date</h2>
            <form method="POST" id="tourForm">
                <input type="hidden" name="action" id="formAction" value="add_tour_date">
                <input type="hidden" name="id" id="tourId">
                
                <div class="form-group">
                    <label class="form-label">Venue *</label>
                    <input type="text" name="venue" class="form-input" id="venue" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">City *</label>
                        <input type="text" name="city" class="form-input" id="city" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Country *</label>
                        <input type="text" name="country" class="form-input" id="country" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Date *</label>
                        <input type="date" name="date" class="form-input" id="date" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Time</label>
                        <input type="time" name="time" class="form-input" id="time">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Price</label>
                        <input type="text" name="price" class="form-input" id="price" placeholder="25.00">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" id="status">
                            <option value="upcoming">Upcoming</option>
                            <option value="soldout">Sold Out</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Ticket URL</label>
                    <input type="url" name="ticket_url" class="form-input" id="ticketUrl" placeholder="https://example.com/tickets">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-textarea" id="description"></textarea>
                </div>
                
                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Add Tour Date</button>
                </div>
            </form>
        </div>
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

        function openModal() {
            document.getElementById('modalTitle').textContent = 'Add Tour Date';
            document.getElementById('formAction').value = 'add_tour_date';
            document.getElementById('submitBtn').textContent = 'Add Tour Date';
            document.getElementById('tourForm').reset();
            document.getElementById('tourModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('tourModal').classList.remove('active');
        }

        function editTourDate(tourDate) {
            document.getElementById('modalTitle').textContent = 'Edit Tour Date';
            document.getElementById('formAction').value = 'update_tour_date';
            document.getElementById('submitBtn').textContent = 'Update Tour Date';
            
            // Fill form with tour date data
            document.getElementById('tourId').value = tourDate.id;
            document.getElementById('venue').value = tourDate.venue;
            document.getElementById('city').value = tourDate.city;
            document.getElementById('country').value = tourDate.country;
            document.getElementById('date').value = tourDate.date;
            document.getElementById('time').value = tourDate.time || '';
            document.getElementById('price').value = tourDate.price || '';
            document.getElementById('status').value = tourDate.status;
            document.getElementById('ticketUrl').value = tourDate.ticket_url || '';
            document.getElementById('description').value = tourDate.description || '';
            
            document.getElementById('tourModal').classList.add('active');
        }

        // Close modal when clicking outside
        document.getElementById('tourModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>
