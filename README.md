# Singer Portfolio Website

A modern, multi-page singer portfolio website built with React.js frontend and PHP/MySQL backend.

## Features

- **Frontend**: React.js with functional components, hooks, and React Router
- **Backend**: PHP REST API with MySQL database
- **Pages**: Home, About, Music, Videos, Gallery, Tour/Events, Contact
- **Design**: Dark, cinematic UI with smooth animations and responsive layout
- **Admin**: Simple admin panel for content management

## Project Structure

```
Portfolio/
├── frontend/          # React.js application
├── backend/           # PHP REST API
├── database/          # MySQL schema and setup
└── README.md
```

## Setup Instructions

### Frontend (React.js)
```bash
cd frontend
npm install
npm start
```

### Backend (PHP)
- Configure your XAMPP/Apache server to point to the `backend` directory
- Set up the MySQL database using the provided schema
- Configure database connection in `backend/config/database.php`

### Database
- Import the SQL file from `database/schema.sql`
- Update database credentials in the PHP configuration

## Technologies Used

- **Frontend**: React.js, React Router, CSS3, Framer Motion
- **Backend**: PHP, MySQL, REST API
- **Styling**: CSS3 with animations, responsive design
- **Features**: Audio players, video embeds, image gallery, contact form
