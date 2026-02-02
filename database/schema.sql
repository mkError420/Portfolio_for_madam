-- Singer Portfolio Database Schema
-- MySQL Database

CREATE DATABASE IF NOT EXISTS singer_portfolio;
USE singer_portfolio;

-- Albums table
CREATE TABLE albums (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    year INT NOT NULL,
    cover_image VARCHAR(500),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tracks table
CREATE TABLE tracks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    album_id INT,
    title VARCHAR(255) NOT NULL,
    duration VARCHAR(10) NOT NULL,
    artist VARCHAR(255) NOT NULL,
    audio_file VARCHAR(500),
    track_number INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (album_id) REFERENCES albums(id) ON DELETE SET NULL
);

-- Singles table
CREATE TABLE singles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    duration VARCHAR(10) NOT NULL,
    artist VARCHAR(255) NOT NULL,
    cover_image VARCHAR(500),
    audio_file VARCHAR(500),
    release_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Videos table
CREATE TABLE videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    video_id VARCHAR(100) NOT NULL, -- YouTube video ID
    thumbnail VARCHAR(500),
    duration VARCHAR(20),
    category ENUM('music_video', 'live_performance', 'behind_scenes') NOT NULL,
    views BIGINT DEFAULT 0,
    release_date DATE,
    venue VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Gallery table
CREATE TABLE gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image_url VARCHAR(500) NOT NULL,
    thumbnail_url VARCHAR(500),
    category ENUM('performance', 'studio', 'behind_scenes') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tour dates table
CREATE TABLE tour_dates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    venue VARCHAR(255) NOT NULL,
    city VARCHAR(255) NOT NULL,
    country VARCHAR(255) NOT NULL,
    status ENUM('upcoming', 'past', 'cancelled') DEFAULT 'upcoming',
    ticket_url VARCHAR(500),
    price_range VARCHAR(100),
    special_notes VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Contact messages table
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message_type ENUM('general', 'booking', 'collaboration', 'press', 'fan') DEFAULT 'general',
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin users table
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'editor') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Insert sample data
-- Albums
INSERT INTO albums (title, year, cover_image, description) VALUES
('Echoes of Emotion', 2024, 'https://via.placeholder.com/300x300/2a2a2a/ffffff?text=Album+1', 'A collection of songs that represent a new chapter in my musical journey.'),
('Soulful Journey', 2022, 'https://via.placeholder.com/300x300/2a2a2a/ffffff?text=Album+2', 'An exploration of soul through melody and rhythm.'),
('Acoustic Sessions', 2020, 'https://via.placeholder.com/300x300/2a2a2a/ffffff?text=Album+3', 'Intimate acoustic performances captured in the studio.');

-- Tracks for Album 1
INSERT INTO tracks (album_id, title, duration, artist, track_number) VALUES
(1, 'Whispers of the Soul', '3:45', 'Artist Name', 1),
(1, 'Midnight Melodies', '4:12', 'Artist Name', 2),
(1, 'Dancing in the Rain', '3:28', 'Artist Name', 3),
(1, 'Heart\'s Symphony', '5:01', 'Artist Name', 4),
(1, 'Eternal Echoes', '4:33', 'Artist Name', 5);

-- Tracks for Album 2
INSERT INTO tracks (album_id, title, duration, artist, track_number) VALUES
(2, 'Journey Begins', '3:15', 'Artist Name', 1),
(2, 'Soul\'s Awakening', '4:45', 'Artist Name', 2),
(2, 'Rhythms of Life', '3:52', 'Artist Name', 3),
(2, 'Emotional Tides', '4:18', 'Artist Name', 4);

-- Tracks for Album 3
INSERT INTO tracks (album_id, title, duration, artist, track_number) VALUES
(3, 'Unplugged Dreams', '3:08', 'Artist Name', 1),
(3, 'Raw Emotions', '4:25', 'Artist Name', 2),
(3, 'Intimate Moments', '3:42', 'Artist Name', 3);

-- Singles
INSERT INTO singles (title, duration, artist, cover_image, release_date) VALUES
('New Beginning', '3:55', 'Artist Name', 'https://via.placeholder.com/300x300/2a2a2a/ffffff?text=Single+1', '2024-01-15'),
('Summer Vibes', '3:22', 'Artist Name', 'https://via.placeholder.com/300x300/2a2a2a/ffffff?text=Single+2', '2024-02-20'),
('Winter\'s Tale', '4:08', 'Artist Name', 'https://via.placeholder.com/300x300/2a2a2a/ffffff?text=Single+3', '2023-12-10');

-- Videos
INSERT INTO videos (title, description, video_id, thumbnail, duration, category, views, release_date) VALUES
('Whispers of the Soul', 'Official music video from the latest album', 'dQw4w9WgXcQ', 'https://via.placeholder.com/640x360/2a2a2a/ffffff?text=Music+Video+1', '3:45', 'music_video', 1200000, '2024-01-15'),
('Midnight Melodies', 'A soulful journey through the night', 'dQw4w9WgXcQ', 'https://via.placeholder.com/640x360/2a2a2a/ffffff?text=Music+Video+2', '4:12', 'music_video', 856000, '2024-02-20'),
('Live at Madison Square Garden', 'Full concert performance from the world tour', 'dQw4w9WgXcQ', 'https://via.placeholder.com/640x360/2a2a2a/ffffff?text=Live+Performance+1', '1:45:00', 'live_performance', 3500000, '2024-03-10'),
('Making of the Album', 'Behind the scenes of the recording process', 'dQw4w9WgXcQ', 'https://via.placeholder.com/640x360/2a2a2a/ffffff?text=Behind+Scenes+1', '12:45', 'behind_scenes', 234000, '2024-01-05');

-- Gallery
INSERT INTO gallery (title, description, image_url, thumbnail_url, category) VALUES
('Live Performance', 'Electric performance at Madison Square Garden', 'https://via.placeholder.com/800x600/2a2a2a/ffffff?text=Performance+1', 'https://via.placeholder.com/400x300/2a2a2a/ffffff?text=Performance+1', 'performance'),
('Studio Session', 'Recording the latest album', 'https://via.placeholder.com/800x600/2a2a2a/ffffff?text=Studio+1', 'https://via.placeholder.com/400x300/2a2a2a/ffffff?text=Studio+1', 'studio'),
('Behind the Scenes', 'Making of the music video', 'https://via.placeholder.com/800x600/2a2a2a/ffffff?text=Behind+Scenes+1', 'https://via.placeholder.com/400x300/2a2a2a/ffffff?text=Behind+Scenes+1', 'behind_scenes'),
('Festival Performance', 'Summer Music Festival 2024', 'https://via.placeholder.com/800x600/2a2a2a/ffffff?text=Performance+2', 'https://via.placeholder.com/400x300/2a2a2a/ffffff?text=Performance+2', 'performance'),
('Acoustic Session', 'Intimate acoustic recording', 'https://via.placeholder.com/800x600/2a2a2a/ffffff?text=Studio+2', 'https://via.placeholder.com/400x300/2a2a2a/ffffff?text=Studio+2', 'studio');

-- Tour dates
INSERT INTO tour_dates (date, venue, city, country, status, ticket_url, price_range, special_notes) VALUES
('2024-03-15', 'Madison Square Garden', 'New York', 'USA', 'upcoming', 'https://example.com/tickets', '$75 - $250', 'Sold Out'),
('2024-03-18', 'Royal Albert Hall', 'London', 'UK', 'upcoming', 'https://example.com/tickets', '$60 - $200', NULL),
('2024-03-22', 'Olympia', 'Paris', 'France', 'upcoming', 'https://example.com/tickets', '$55 - $180', NULL),
('2024-03-25', 'Tokyo Dome', 'Tokyo', 'Japan', 'upcoming', 'https://example.com/tickets', '$80 - $300', NULL),
('2024-02-28', 'Sydney Opera House', 'Sydney', 'Australia', 'past', NULL, '$70 - $220', NULL),
('2024-02-20', 'Red Rocks Amphitheatre', 'Colorado', 'USA', 'past', NULL, '$65 - $190', NULL);

-- Admin user (password: admin123)
INSERT INTO admin_users (username, email, password_hash, role) VALUES
('admin', 'admin@singerportfolio.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Create indexes for better performance
CREATE INDEX idx_albums_year ON albums(year);
CREATE INDEX idx_tracks_album ON tracks(album_id);
CREATE INDEX idx_videos_category ON videos(category);
CREATE INDEX idx_gallery_category ON gallery(category);
CREATE INDEX idx_tour_dates_status ON tour_dates(status);
CREATE INDEX idx_tour_dates_date ON tour_dates(date);
CREATE INDEX idx_contact_messages_status ON contact_messages(status);
