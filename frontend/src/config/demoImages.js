// Demo images configuration
// Replace these URLs with your actual images

export const demoImages = {
  // Hero and general
  hero: "https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=1920&h=1080&fit=crop",
  artist: "https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=400&h=500&fit=crop",
  
  // Album covers
  albums: {
    1: "https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=300&h=300&fit=crop",
    2: "https://images.unsplash.com/photo-1471478653858-1b1b38920218?w=300&h=300&fit=crop",
    3: "https://images.unsplash.com/photo-1459749411175-04bf5292ceea?w=300&h=300&fit=crop"
  },
  
  // Single covers
  singles: {
    1: "https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=300&h=300&fit=crop",
    2: "https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=300&h=300&fit=crop",
    3: "https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=300&h=300&fit=crop"
  },
  
  // Gallery images
  gallery: [
    {
      id: 1,
      src: "https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=800&h=600&fit=crop",
      thumbnail: "https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=400&h=300&fit=crop",
      title: "Live Performance",
      category: "performance",
      description: "Electric performance at Madison Square Garden"
    },
    {
      id: 2,
      src: "https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=800&h=600&fit=crop",
      thumbnail: "https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=400&h=300&fit=crop",
      title: "Studio Session",
      category: "studio",
      description: "Recording the latest album"
    },
    {
      id: 3,
      src: "https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=800&h=600&fit=crop",
      thumbnail: "https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=400&h=300&fit=crop",
      title: "Behind the Scenes",
      category: "behind",
      description: "Making of the music video"
    },
    {
      id: 4,
      src: "https://images.unsplash.com/photo-1459749411175-04bf5292ceea?w=800&h=600&fit=crop",
      thumbnail: "https://images.unsplash.com/photo-1459749411175-04bf5292ceea?w=400&h=300&fit=crop",
      title: "Festival Performance",
      category: "performance",
      description: "Summer Music Festival 2024"
    },
    {
      id: 5,
      src: "https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=800&h=600&fit=crop",
      thumbnail: "https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=400&h=300&fit=crop",
      title: "Acoustic Session",
      category: "studio",
      description: "Intimate acoustic recording"
    },
    {
      id: 6,
      src: "https://images.unsplash.com/photo-1471478653858-1b1b38920218?w=800&h=600&fit=crop",
      thumbnail: "https://images.unsplash.com/photo-1471478653858-1b1b38920218?w=400&h=300&fit=crop",
      title: "Tour Life",
      category: "behind",
      description: "Life on the road"
    }
  ],
  
  // Video thumbnails
  videos: {
    music1: "https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=640&h=360&fit=crop",
    music2: "https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=640&h=360&fit=crop",
    music3: "https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=640&h=360&fit=crop",
    live1: "https://images.unsplash.com/photo-1459749411175-04bf5292ceea?w=640&h=360&fit=crop",
    live2: "https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=640&h=360&fit=crop",
    live3: "https://images.unsplash.com/photo-1471478653858-1b1b38920218?w=640&h=360&fit=crop",
    behind1: "https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=640&h=360&fit=crop",
    behind2: "https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=640&h=360&fit=crop",
    behind3: "https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=640&h=360&fit=crop"
  }
};

// Helper function to get image with fallback
export const getImageWithFallback = (imageUrl, fallbackText = 'Image') => {
  return imageUrl || `https://via.placeholder.com/400x300/2a2a2a/ffffff?text=${encodeURIComponent(fallbackText)}`;
};
