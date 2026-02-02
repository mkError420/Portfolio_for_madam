import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { galleryAPI } from '../services/api';

const Gallery = () => {
  const [selectedImage, setSelectedImage] = useState(null);
  const [filter, setFilter] = useState('all');
  const [galleryImages, setGalleryImages] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchGalleryImages();
  }, []);

  const fetchGalleryImages = async () => {
    try {
      setLoading(true);
      // Try to fetch from API first
      const data = await galleryAPI.getAllImages();
      setGalleryImages(data);
    } catch (error) {
      console.error('Error fetching gallery images:', error);
      // Fallback to reliable placeholder images
      setGalleryImages([
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
      ]);
    } finally {
      setLoading(false);
    }
  };

  const categories = [
    { id: 'all', label: 'All Photos' },
    { id: 'performance', label: 'Performances' },
    { id: 'studio', label: 'Studio' },
    { id: 'behind', label: 'Behind Scenes' }
  ];

  const filteredImages = filter === 'all' 
    ? galleryImages 
    : galleryImages.filter(img => img.category === filter);

  const openLightbox = (image) => {
    setSelectedImage(image);
  };

  const closeLightbox = () => {
    setSelectedImage(null);
  };

  const navigateImage = (direction) => {
    const currentIndex = filteredImages.findIndex(img => img.id === selectedImage.id);
    let newIndex;
    
    if (direction === 'next') {
      newIndex = currentIndex < filteredImages.length - 1 ? currentIndex + 1 : 0;
    } else {
      newIndex = currentIndex > 0 ? currentIndex - 1 : filteredImages.length - 1;
    }
    
    setSelectedImage(filteredImages[newIndex]);
  };

  return (
    <div className="gallery">
      {/* Hero Section */}
      <section className="gallery-hero" style={{
        padding: '8rem 0 4rem',
        background: 'linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%)',
        textAlign: 'center',
      }}>
        <div className="container">
          <motion.div
            initial={{ opacity: 0, y: 50 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8 }}
          >
            <h1 style={{
              fontFamily: "'Playfair Display', serif",
              fontSize: 'clamp(2.5rem, 5vw, 4rem)',
              color: 'var(--text-primary)',
              marginBottom: '1rem',
            }}>
              Gallery
            </h1>
            <p style={{
              fontSize: '1.2rem',
              color: 'var(--text-secondary)',
              maxWidth: '600px',
              margin: '0 auto',
              lineHeight: 1.6,
            }}>
              Visual memories from performances, studio sessions, and behind the scenes
            </p>
          </motion.div>
        </div>
      </section>

      {/* Filter Buttons */}
      <section className="gallery-filters" style={{
        padding: '2rem 0',
        background: 'var(--primary-color)',
      }}>
        <div className="container">
          <div style={{
            display: 'flex',
            justifyContent: 'center',
            gap: '1rem',
            flexWrap: 'wrap',
          }}>
            {categories.map((category) => (
              <button
                key={category.id}
                onClick={() => setFilter(category.id)}
                className={`btn ${filter === category.id ? 'btn-primary' : 'btn-secondary'}`}
                style={{
                  fontSize: '0.9rem',
                  padding: '0.8rem 1.5rem',
                }}
              >
                {category.label}
              </button>
            ))}
          </div>
        </div>
      </section>

      {/* Gallery Grid */}
      <section className="gallery-grid" style={{
        padding: '3rem 0',
        background: 'var(--secondary-color)',
      }}>
        <div className="container">
          <motion.div
            layout
            style={{
              display: 'grid',
              gridTemplateColumns: 'repeat(auto-fill, minmax(300px, 1fr))',
              gap: '1.5rem',
            }}
          >
            {loading ? (
              // Loading state
              Array.from({ length: 6 }).map((_, index) => (
                <div
                  key={`loading-${index}`}
                  className="card"
                  style={{
                    cursor: 'pointer',
                    overflow: 'hidden',
                    padding: 0,
                  }}
                >
                  <div style={{
                    width: '100%',
                    height: '250px',
                    background: 'linear-gradient(90deg, #2a2a2a 25%, #3a3a3a 50%, #2a2a2a 75%)',
                    backgroundSize: '200% 100%',
                    animation: 'shimmer 1.5s infinite',
                  }} />
                </div>
              ))
            ) : (
              <AnimatePresence>
                {filteredImages.map((image, index) => (
                  <motion.div
                    key={image.id}
                    layout
                    initial={{ opacity: 0, scale: 0.8 }}
                    animate={{ opacity: 1, scale: 1 }}
                    exit={{ opacity: 0, scale: 0.8 }}
                    transition={{ duration: 0.5, delay: index * 0.1 }}
                    whileHover={{ scale: 1.03 }}
                    className="card"
                    style={{
                      cursor: 'pointer',
                      overflow: 'hidden',
                      padding: 0,
                    }}
                    onClick={() => openLightbox(image)}
                  >
                    <div style={{ position: 'relative', overflow: 'hidden' }}>
                      <img
                        src={image.thumbnail || image.src}
                        alt={image.title}
                        onError={(e) => {
                          // Fallback to a reliable image if the main one fails
                          e.target.src = `https://via.placeholder.com/400x300/2a2a2a/ffffff?text=${encodeURIComponent(image.title)}`;
                        }}
                        style={{
                          width: '100%',
                          height: '250px',
                          objectFit: 'cover',
                          transition: 'transform 0.3s ease',
                        }}
                      />
                      <div style={{
                        position: 'absolute',
                        inset: 0,
                        background: 'linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 50%)',
                        opacity: 0,
                        transition: 'opacity 0.3s ease',
                      }} />
                    <div style={{
                      position: 'absolute',
                      bottom: 0,
                      left: 0,
                      right: 0,
                      padding: '1.5rem',
                      transform: 'translateY(20px)',
                      transition: 'transform 0.3s ease',
                    }}>
                      <h3 style={{
                        color: 'var(--text-primary)',
                        margin: '0 0 0.5rem',
                        fontSize: '1.1rem',
                      }}>
                        {image.title}
                      </h3>
                      <p style={{
                        color: 'var(--text-secondary)',
                        margin: 0,
                        fontSize: '0.9rem',
                      }}>
                        {image.description}
                      </p>
                    </div>
                  </div>
                </motion.div>
              ))}
            </AnimatePresence>
            )}
          </motion.div>
        </div>
      </section>

      {/* Lightbox */}
      <AnimatePresence>
        {selectedImage && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            onClick={closeLightbox}
            style={{
              position: 'fixed',
              top: 0,
              left: 0,
              right: 0,
              bottom: 0,
              background: 'rgba(0, 0, 0, 0.95)',
              display: 'flex',
              alignItems: 'center',
              justifyContent: 'center',
              zIndex: 2000,
              padding: '2rem',
            }}
          >
            <motion.div
              initial={{ scale: 0.8 }}
              animate={{ scale: 1 }}
              exit={{ scale: 0.8 }}
              onClick={(e) => e.stopPropagation()}
              style={{
                width: '100%',
                maxWidth: '900px',
                maxHeight: '90vh',
                display: 'flex',
                flexDirection: 'column',
              }}
            >
              {/* Image */}
              <div style={{
                position: 'relative',
                flex: 1,
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
              }}>
                <img
                  src={selectedImage.src || selectedImage.thumbnail}
                  alt={selectedImage.title}
                  onError={(e) => {
                    e.target.src = `https://via.placeholder.com/800x600/2a2a2a/ffffff?text=${encodeURIComponent(selectedImage.title)}`;
                  }}
                  style={{
                    maxWidth: '100%',
                    maxHeight: '70vh',
                    objectFit: 'contain',
                    borderRadius: '10px',
                  }}
                />

                {/* Navigation Buttons */}
                <button
                  onClick={() => navigateImage('prev')}
                  style={{
                    position: 'absolute',
                    left: '20px',
                    top: '50%',
                    transform: 'translateY(-50%)',
                    background: 'rgba(255, 255, 255, 0.1)',
                    border: 'none',
                    color: 'white',
                    fontSize: '2rem',
                    width: '50px',
                    height: '50px',
                    borderRadius: '50%',
                    cursor: 'pointer',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    backdropFilter: 'blur(10px)',
                  }}
                >
                  ‹
                </button>
                <button
                  onClick={() => navigateImage('next')}
                  style={{
                    position: 'absolute',
                    right: '20px',
                    top: '50%',
                    transform: 'translateY(-50%)',
                    background: 'rgba(255, 255, 255, 0.1)',
                    border: 'none',
                    color: 'white',
                    fontSize: '2rem',
                    width: '50px',
                    height: '50px',
                    borderRadius: '50%',
                    cursor: 'pointer',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    backdropFilter: 'blur(10px)',
                  }}
                >
                  ›
                </button>

                {/* Close Button */}
                <button
                  onClick={closeLightbox}
                  style={{
                    position: 'absolute',
                    top: '20px',
                    right: '20px',
                    background: 'rgba(255, 255, 255, 0.1)',
                    border: 'none',
                    color: 'white',
                    fontSize: '1.5rem',
                    width: '40px',
                    height: '40px',
                    borderRadius: '50%',
                    cursor: 'pointer',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    backdropFilter: 'blur(10px)',
                  }}
                >
                  ×
                </button>
              </div>

              {/* Image Info */}
              <div style={{
                padding: '1.5rem',
                textAlign: 'center',
                background: 'rgba(26, 26, 26, 0.9)',
                borderRadius: '0 0 10px 10px',
                backdropFilter: 'blur(10px)',
              }}>
                <h3 style={{
                  color: 'var(--text-primary)',
                  margin: '0 0 0.5rem',
                  fontSize: '1.3rem',
                }}>
                  {selectedImage.title}
                </h3>
                <p style={{
                  color: 'var(--text-secondary)',
                  margin: 0,
                  fontSize: '1rem',
                }}>
                  {selectedImage.description}
                </p>
              </div>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
};

export default Gallery;
