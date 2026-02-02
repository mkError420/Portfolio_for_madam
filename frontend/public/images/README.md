# Demo Images

This folder contains placeholder files for demo images. 

## Current Setup

The application uses high-quality Unsplash images for demo purposes. These are configured in `src/config/demoImages.js`.

## Image Categories

### Hero & General
- `hero-bg.jpg` - Hero background image
- `artist-photo.jpg` - Artist profile photo

### Album Covers
- `album1.jpg` - "Echoes of Emotion" album
- `album2.jpg` - "Soulful Journey" album  
- `album3.jpg` - "Acoustic Sessions" album

### Single Covers
- `single1.jpg` - "New Beginning" single
- `single2.jpg` - "Summer Vibes" single
- `single3.jpg` - "Winter's Tale" single

## How to Replace with Your Own Images

1. **Replace the image files** in this folder with your actual images
2. **Update the paths** in `src/config/demoImages.js` to point to your local files:

```javascript
// Change from:
hero: "https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=1920&h=1080&fit=crop",

// To:
hero: "/images/demo/hero-bg.jpg",
```

3. **Recommended image sizes:**
- Hero background: 1920x1080px
- Album covers: 300x300px (square)
- Artist photo: 400x500px
- Gallery images: 800x600px (large), 400x300px (thumbnails)
- Video thumbnails: 640x360px

## Current Demo Images

The application currently uses these Unsplash images:
- Hero: Concert stage lighting
- Artist: Person with guitar
- Albums: Various music-related scenes
- Gallery: Performance, studio, and behind-the-scenes photos
- Videos: Music and performance thumbnails

All images are properly licensed for demo use and will work immediately without any setup required.
