-- Fix Albums Table - Run this in phpMyAdmin

-- Add missing columns to albums table
ALTER TABLE albums ADD COLUMN IF NOT EXISTS artist VARCHAR(255) NOT NULL DEFAULT '' AFTER title;
ALTER TABLE albums ADD COLUMN IF NOT EXISTS release_date DATE NULL AFTER artist;
ALTER TABLE albums ADD COLUMN IF NOT EXISTS genre VARCHAR(100) DEFAULT '' AFTER release_date;
ALTER TABLE albums ADD COLUMN IF NOT EXISTS type ENUM('album', 'single', 'ep') DEFAULT 'album' AFTER genre;
ALTER TABLE albums ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER type;
ALTER TABLE albums ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

-- Show the final structure
DESCRIBE albums;
