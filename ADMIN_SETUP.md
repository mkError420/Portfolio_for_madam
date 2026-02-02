# Admin Dashboard Setup Guide

## Overview
The Singer Portfolio Admin Dashboard is a comprehensive content management system that allows you to dynamically manage all aspects of your website including music, videos, gallery, tour dates, and contact messages.

## Features

### 🎵 **Music Management**
- Add/Edit/Delete Albums with track listings
- Manage Singles with release information
- Upload album covers and artwork
- Organize tracks by album

### 🎬 **Video Management**
- Add YouTube/Vimeo videos
- Categorize videos (Music Videos, Live Performances, Behind Scenes)
- Manage video thumbnails and metadata
- Track view counts and release dates

### 🖼️ **Gallery Management**
- Upload images directly or use URLs
- Categorize gallery images (Performances, Studio, Behind Scenes)
- Automatic thumbnail generation
- Bulk image management

### 📅 **Tour Management**
- Add upcoming and past tour dates
- Manage venue information and ticket links
- Track show status (Upcoming, Sold Out, Cancelled)
- Automatic past/upcoming separation

### 📧 **Message Management**
- View and manage contact form submissions
- Mark messages as read/unread
- Reply directly to contacts
- Bulk message operations

### ⚙️ **Settings & Security**
- Change admin passwords
- Manage admin user accounts
- Profile management
- Secure authentication system

## Quick Setup

### 1. Database Setup
```sql
-- Import the database schema
-- File: database/schema.sql
```

### 2. Admin User Setup
Default admin credentials:
- **Username**: `admin`
- **Password**: `admin123`

⚠️ **Important**: Change the default password immediately after first login!

### 3. Access the Dashboard
Navigate to: `http://localhost/backend/admin/login.php`

## Detailed Guide

### Music Management

#### Adding an Album
1. Go to **Music** → Click **Add Album**
2. Fill in album details:
   - Album Title (required)
   - Release Year
   - Description
   - Cover Image URL
3. Add tracks:
   - Track number
   - Track title
   - Duration
4. Click **Add Album**

#### Adding a Single
1. Go to **Music** → Click **Add Single**
2. Fill in single details:
   - Single Title (required)
   - Artist name
   - Duration
   - Release Date
   - Cover Image URL
3. Click **Add Single**

### Video Management

#### Adding a Video
1. Go to **Videos** → Click **Add Video**
2. Fill in video details:
   - Title (required)
   - Description
   - Category (Music Video/Live/Behind Scenes)
   - Video URL (YouTube/Vimeo)
   - Thumbnail URL
   - Duration
   - Views
   - Release Date
3. Click **Add Video**

**YouTube URL Formats:**
- `https://www.youtube.com/watch?v=VIDEO_ID`
- `https://youtu.be/VIDEO_ID`

### Gallery Management

#### Adding Images
1. Go to **Gallery** → Click **Add Image**
2. Choose upload method:
   - **File Upload**: Direct upload from computer
   - **URL**: Link to existing image
3. Fill in details:
   - Title (required)
   - Description
   - Category (Performance/Studio/Behind Scenes)
   - Image file or URL
   - Thumbnail (optional)
4. Click **Add Image**

**Supported Formats:** JPG, PNG, GIF, WebP
**Max File Size:** 10MB

### Tour Management

#### Adding Tour Dates
1. Go to **Tour** → Click **Add Tour Date**
2. Fill in event details:
   - Venue Name (required)
   - City (required)
   - Country (required)
   - Date (required)
   - Time
   - Ticket URL
   - Price
   - Status (Upcoming/Sold Out/Cancelled)
   - Description
3. Click **Add Tour Date**

**Tabs:**
- **Upcoming Shows**: Future tour dates
- **Past Shows**: Completed events
- **All Shows**: Complete tour history

### Message Management

#### Managing Contact Messages
1. Go to **Messages**
2. View tabs:
   - **All Messages**: Complete message list
   - **Unread**: New messages only
   - **Read**: Previously viewed messages
3. Actions available:
   - Mark as Read/Unread
   - Reply via email
   - Delete individual messages
   - Bulk operations

### Settings & Security

#### Changing Password
1. Go to **Settings**
2. In **Change Password** section:
   - Enter current password
   - Enter new password (min 8 characters)
   - Confirm new password
3. Click **Change Password**

#### Updating Profile
1. Go to **Settings**
2. In **Profile Settings** section:
   - Update username
   - Update email
3. Click **Update Profile**

#### Managing Admin Users
1. Go to **Settings**
2. View current admin users
3. Delete users (except yourself)
4. **Note**: Currently only deletion is supported via UI

## File Structure

```
backend/admin/
├── dashboard.php      # Main dashboard with stats
├── music.php          # Music management
├── videos.php         # Video management
├── gallery.php        # Gallery management
├── tour.php           # Tour management
├── messages.php       # Message management
├── settings.php       # Settings & security
├── login.php          # Secure login
├── logout.php         # Logout functionality
└── uploads/           # File upload directory
    ├── gallery/
    │   ├── images/
    │   └── thumbnails/
    └── ... (other upload folders)
```

## Security Features

### Authentication
- Secure password hashing (bcrypt)
- Session-based authentication
- Login attempt delays (brute force protection)
- Auto-logout on session expiration

### File Upload Security
- File type validation
- Size restrictions (10MB max)
- Secure file naming (unique IDs)
- Directory permissions

### Input Validation
- SQL injection prevention (prepared statements)
- XSS protection (output escaping)
- CSRF protection (form tokens)
- Input sanitization

## API Integration

The dashboard works seamlessly with the frontend API:

### Music API
- Albums: `/api/music`
- Singles: Integrated with albums

### Gallery API
- Images: `/api/gallery`
- Category filtering

### Tour API
- Dates: `/api/tour`
- Status filtering

### Contact API
- Messages: `/api/contact`

## Troubleshooting

### Login Issues
- Verify database connection
- Check admin user exists
- Reset password if needed

### File Upload Issues
- Check upload directory permissions
- Verify PHP upload limits
- Check disk space

### API Connection Issues
- Verify CORS configuration
- Check database credentials
- Test API endpoints directly

## Best Practices

### Security
1. Change default password immediately
2. Use strong passwords (8+ characters, mixed case, numbers, symbols)
3. Regularly update admin passwords
4. Limit admin user accounts
5. Keep PHP and server updated

### Content Management
1. Use descriptive titles and descriptions
2. Organize content with categories
3. Optimize images for web (compress before upload)
4. Regular backup of database
5. Test content on frontend after changes

### Performance
1. Don't upload excessively large images
2. Use appropriate image formats (JPG for photos, PNG for graphics)
3. Regular cleanup of old content
4. Monitor database size

## Support

For issues or questions:
1. Check this documentation first
2. Verify database and server configuration
3. Test API endpoints individually
4. Check browser console for errors

## Next Steps

After setting up the admin dashboard:

1. ✅ Change default password
2. ✅ Add your first album and tracks
3. ✅ Upload gallery images
4. ✅ Add tour dates
5. ✅ Test contact form functionality
6. ✅ Explore all features
7. ✅ Customize content for your needs

The admin dashboard is now ready for managing your singer portfolio website! 🎵
