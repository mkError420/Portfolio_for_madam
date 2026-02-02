# Singer Portfolio Website - Setup Guide

## Overview
A modern, multi-page singer portfolio website built with React.js frontend and PHP/MySQL backend.

## Features
- **Frontend**: React.js with functional components, hooks, and React Router
- **Backend**: PHP REST API with MySQL database
- **Pages**: Home, About, Music, Videos, Gallery, Tour/Events, Contact
- **Design**: Dark, cinematic UI with smooth animations and responsive layout
- **Admin**: Simple admin panel for content management

## Prerequisites
- XAMPP (or similar PHP/MySQL environment)
- Node.js and npm
- Modern web browser

## Setup Instructions

### 1. Database Setup

1. Start XAMPP and ensure Apache and MySQL are running
2. Open phpMyAdmin (http://localhost/phpmyadmin)
3. Create a new database named `singer_portfolio`
4. Import the SQL file:
   - Navigate to `database/schema.sql`
   - Import this file into the `singer_portfolio` database

### 2. Backend Setup

1. Configure Apache to point to the `backend` directory:
   - Open XAMPP Control Panel
   - Click "Config" for Apache → "Apache (httpd.conf)"
   - Find `DocumentRoot` and change it to your backend path
   - Example: `DocumentRoot "c:/xampp/htdocs/Portfolio/backend"`
   - Also update the `<Directory>` path below it

2. Enable mod_rewrite in Apache:
   - Uncomment `LoadModule rewrite_module modules/mod_rewrite.so` in httpd.conf

3. Test the API:
   - Visit `http://localhost/api` in your browser
   - You should see the API documentation

### 3. Frontend Setup

1. Open terminal/command prompt
2. Navigate to the frontend directory:
   ```bash
   cd c:/xampp/htdocs/Portfolio/frontend
   ```

3. Install dependencies:
   ```bash
   npm install
   ```

4. Start the development server:
   ```bash
   npm start
   ```

5. The app will open at `http://localhost:3000`

### 4. Admin Panel Access

1. Visit `http://localhost/admin` in your browser
2. Login with:
   - Username: `admin`
   - Password: `admin123`

## File Structure

```
Portfolio/
├── frontend/                 # React.js application
│   ├── public/              # Static files
│   ├── src/
│   │   ├── components/      # Reusable components
│   │   ├── pages/          # Page components
│   │   ├── services/       # API services
│   │   └── styles/         # CSS files
│   └── package.json
├── backend/                 # PHP REST API
│   ├── api/               # API endpoints
│   │   ├── music/         # Music data
│   │   ├── videos/        # Video data
│   │   ├── gallery/       # Gallery images
│   │   ├── tour/          # Tour dates
│   │   └── contact/       # Contact form
│   ├── admin/             # Admin panel
│   ├── config/            # Configuration files
│   └── index.php          # API router
├── database/              # Database schema
│   └── schema.sql         # SQL setup file
└── README.md
```

## API Endpoints

- `GET /api/music` - Get all albums and singles
- `GET /api/videos` - Get all videos grouped by category
- `GET /api/videos?category=music_video` - Get videos by category
- `GET /api/gallery` - Get all gallery images
- `GET /api/gallery?category=performance` - Get images by category
- `GET /api/tour` - Get all tour dates
- `GET /api/tour?status=upcoming` - Get tour dates by status
- `POST /api/contact` - Submit contact form

## Customization

### 1. Update Artist Information
- Edit the artist name in `frontend/src/components/Navbar.js`
- Update social media links in `frontend/src/components/Footer.js`
- Modify content in individual page components

### 2. Replace Placeholder Images
- Replace placeholder URLs with actual image URLs
- Update database records through the admin panel
- Or directly modify the database

### 3. Customize Colors and Styling
- Edit CSS variables in `frontend/src/styles/global.css`
- Modify component-specific styles as needed

### 4. Add Real Audio/Video
- Replace placeholder audio URLs in the Music page
- Update YouTube video IDs in the database
- Add actual media files to your server

## Production Deployment

### Frontend
1. Build the React app:
   ```bash
   npm run build
   ```
2. Deploy the `build` folder to your web server

### Backend
1. Update database credentials in `backend/config/database.php`
2. Configure your web server to handle PHP requests
3. Set up proper URL rewriting for the API

### Security Considerations
- Change default admin password
- Implement HTTPS
- Add input validation and sanitization
- Set up proper error handling
- Configure CORS for production domains

## Troubleshooting

### Common Issues

1. **API not working**
   - Check if Apache and MySQL are running
   - Verify database connection details
   - Check PHP error logs

2. **Frontend not loading**
   - Ensure all dependencies are installed
   - Check for JavaScript errors in browser console
   - Verify API endpoints are accessible

3. **Database connection errors**
   - Verify MySQL is running
   - Check database name and credentials
   - Ensure the database was created and imported

4. **Admin panel not accessible**
   - Check if session is working
   - Verify file permissions
   - Check PHP error logs

## Support

For issues and questions:
1. Check browser console for JavaScript errors
2. Review PHP error logs in XAMPP
3. Verify all file paths and configurations
4. Ensure all prerequisites are properly installed

## License

This project is open source. Feel free to modify and distribute according to your needs.
