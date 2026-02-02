import React, { useState } from 'react';
import { motion } from 'framer-motion';
import { demoImages } from '../config/demoImages';

const Videos = () => {
  const [selectedVideo, setSelectedVideo] = useState(null);

  const musicVideos = [
    {
      id: 1,
      title: "Whispers of the Soul",
      description: "Official music video from the latest album",
      thumbnail: demoImages.videos.music1,
      videoId: "dQw4w9WgXcQ", // YouTube video ID
      duration: "3:45",
      views: "1.2M",
      releaseDate: "2024"
    },
    {
      id: 2,
      title: "Midnight Melodies",
      description: "A soulful journey through the night",
      thumbnail: demoImages.videos.music2,
      videoId: "dQw4w9WgXcQ",
      duration: "4:12",
      views: "856K",
      releaseDate: "2024"
    },
    {
      id: 3,
      title: "Dancing in the Rain",
      description: "An uplifting celebration of life",
      thumbnail: demoImages.videos.music3,
      videoId: "dQw4w9WgXcQ",
      duration: "3:28",
      views: "2.1M",
      releaseDate: "2023"
    }
  ];

  const livePerformances = [
    {
      id: 4,
      title: "Live at Madison Square Garden",
      description: "Full concert performance from the world tour",
      thumbnail: demoImages.videos.live1,
      videoId: "dQw4w9WgXcQ",
      duration: "1:45:00",
      views: "3.5M",
      releaseDate: "2024",
      venue: "Madison Square Garden, New York"
    },
    {
      id: 5,
      title: "Acoustic Session",
      description: "Intimate acoustic performance",
      thumbnail: demoImages.videos.live2,
      videoId: "dQw4w9WgXcQ",
      duration: "45:30",
      views: "567K",
      releaseDate: "2023",
      venue: "Studio Session"
    },
    {
      id: 6,
      title: "Festival Performance",
      description: "Electric performance at Summer Music Festival",
      thumbnail: demoImages.videos.live3,
      videoId: "dQw4w9WgXcQ",
      duration: "1:20:00",
      views: "1.8M",
      releaseDate: "2023",
      venue: "Summer Music Festival"
    }
  ];

  const behindTheScenes = [
    {
      id: 7,
      title: "Making of the Album",
      description: "Behind the scenes of the recording process",
      thumbnail: demoImages.videos.behind1,
      videoId: "dQw4w9WgXcQ",
      duration: "12:45",
      views: "234K",
      releaseDate: "2024"
    },
    {
      id: 8,
      title: "Music Video Shoot",
      description: "The making of 'Whispers of the Soul' video",
      thumbnail: demoImages.videos.behind2,
      videoId: "dQw4w9WgXcQ",
      duration: "8:30",
      views: "189K",
      releaseDate: "2024"
    },
    {
      id: 9,
      title: "Tour Diary",
      description: "Life on the road during the world tour",
      thumbnail: demoImages.videos.behind3,
      videoId: "dQw4w9WgXcQ",
      duration: "15:20",
      views: "445K",
      releaseDate: "2023"
    }
  ];

  const allVideos = [...musicVideos, ...livePerformances, ...behindTheScenes];

  const openVideoModal = (video) => {
    setSelectedVideo(video);
  };

  const closeModal = () => {
    setSelectedVideo(null);
  };

  return (
    <div className="videos">
      {/* Hero Section */}
      <section className="videos-hero" style={{
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
              Videos
            </h1>
            <p style={{
              fontSize: '1.2rem',
              color: 'var(--text-secondary)',
              maxWidth: '600px',
              margin: '0 auto',
              lineHeight: 1.6,
            }}>
              Music videos, live performances, and exclusive behind-the-scenes content
            </p>
          </motion.div>
        </div>
      </section>

      {/* Featured Video */}
      <section className="featured-video" style={{
        padding: '3rem 0',
        background: 'var(--primary-color)',
      }}>
        <div className="container">
          <motion.div
            initial={{ opacity: 0, scale: 0.9 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ duration: 0.8 }}
            style={{
              position: 'relative',
              paddingBottom: '56.25%',
              height: 0,
              overflow: 'hidden',
              borderRadius: '15px',
              boxShadow: '0 30px 60px rgba(0, 0, 0, 0.4)',
            }}
          >
            <iframe
              src="https://www.youtube.com/embed/dQw4w9WgXcQ"
              title="Featured Video"
              style={{
                position: 'absolute',
                top: 0,
                left: 0,
                width: '100%',
                height: '100%',
                border: 'none',
                borderRadius: '15px',
              }}
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
              allowFullScreen
            />
          </motion.div>
          <motion.div
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8, delay: 0.3 }}
            style={{ textAlign: 'center', marginTop: '2rem' }}
          >
            <h2 style={{ color: 'var(--text-primary)', marginBottom: '0.5rem' }}>
              {musicVideos[0].title}
            </h2>
            <p style={{ color: 'var(--text-secondary)', marginBottom: '1rem' }}>
              {musicVideos[0].description}
            </p>
            <div style={{
              display: 'flex',
              justifyContent: 'center',
              gap: '2rem',
              fontSize: '0.9rem',
              color: 'var(--text-muted)',
            }}>
              <span>{musicVideos[0].views} views</span>
              <span>•</span>
              <span>{musicVideos[0].duration}</span>
              <span>•</span>
              <span>{musicVideos[0].releaseDate}</span>
            </div>
          </motion.div>
        </div>
      </section>

      {/* Music Videos */}
      <section className="music-videos" style={{
        padding: '5rem 0',
        background: 'var(--secondary-color)',
      }}>
        <div className="container">
          <motion.div
            initial={{ opacity: 0, y: 50 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8 }}
            viewport={{ once: true }}
            style={{ textAlign: 'center', marginBottom: '3rem' }}
          >
            <h2 style={{ color: 'var(--text-primary)', marginBottom: '1rem' }}>
              Music Videos
            </h2>
            <p style={{ color: 'var(--text-secondary)', fontSize: '1.1rem' }}>
              Official music videos and visual stories
            </p>
          </motion.div>

          <div style={{
            display: 'grid',
            gridTemplateColumns: 'repeat(auto-fit, minmax(350px, 1fr))',
            gap: '2rem',
          }}>
            {musicVideos.map((video, index) => (
              <motion.div
                key={video.id}
                initial={{ opacity: 0, y: 50 }}
                whileInView={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.8, delay: index * 0.1 }}
                viewport={{ once: true }}
                className="card"
                style={{ cursor: 'pointer' }}
                onClick={() => openVideoModal(video)}
              >
                <div style={{ position: 'relative', marginBottom: '1rem' }}>
                  <img
                    src={video.thumbnail}
                    alt={video.title}
                    style={{
                      width: '100%',
                      borderRadius: '10px',
                      display: 'block',
                    }}
                  />
                  <div style={{
                    position: 'absolute',
                    top: '50%',
                    left: '50%',
                    transform: 'translate(-50%, -50%)',
                    width: '60px',
                    height: '60px',
                    background: 'rgba(255, 107, 107, 0.9)',
                    borderRadius: '50%',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    fontSize: '1.5rem',
                    color: 'white',
                    transition: 'all 0.3s ease',
                  }}>
                    ▶
                  </div>
                  <span style={{
                    position: 'absolute',
                    bottom: '10px',
                    right: '10px',
                    background: 'rgba(0, 0, 0, 0.8)',
                    color: 'white',
                    padding: '4px 8px',
                    borderRadius: '4px',
                    fontSize: '0.8rem',
                  }}>
                    {video.duration}
                  </span>
                </div>
                <h3 style={{ color: 'var(--text-primary)', marginBottom: '0.5rem' }}>
                  {video.title}
                </h3>
                <p style={{ color: 'var(--text-secondary)', marginBottom: '1rem', fontSize: '0.95rem' }}>
                  {video.description}
                </p>
                <div style={{
                  display: 'flex',
                  justifyContent: 'space-between',
                  fontSize: '0.85rem',
                  color: 'var(--text-muted)',
                }}>
                  <span>{video.views} views</span>
                  <span>{video.releaseDate}</span>
                </div>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* Live Performances */}
      <section className="live-performances" style={{
        padding: '5rem 0',
        background: 'var(--primary-color)',
      }}>
        <div className="container">
          <motion.div
            initial={{ opacity: 0, y: 50 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8 }}
            viewport={{ once: true }}
            style={{ textAlign: 'center', marginBottom: '3rem' }}
          >
            <h2 style={{ color: 'var(--text-primary)', marginBottom: '1rem' }}>
              Live Performances
            </h2>
            <p style={{ color: 'var(--text-secondary)', fontSize: '1.1rem' }}>
              Experience the energy of live shows
            </p>
          </motion.div>

          <div style={{
            display: 'grid',
            gridTemplateColumns: 'repeat(auto-fit, minmax(350px, 1fr))',
            gap: '2rem',
          }}>
            {livePerformances.map((video, index) => (
              <motion.div
                key={video.id}
                initial={{ opacity: 0, y: 50 }}
                whileInView={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.8, delay: index * 0.1 }}
                viewport={{ once: true }}
                className="card"
                style={{ cursor: 'pointer' }}
                onClick={() => openVideoModal(video)}
              >
                <div style={{ position: 'relative', marginBottom: '1rem' }}>
                  <img
                    src={video.thumbnail}
                    alt={video.title}
                    style={{
                      width: '100%',
                      borderRadius: '10px',
                      display: 'block',
                    }}
                  />
                  <div style={{
                    position: 'absolute',
                    top: '50%',
                    left: '50%',
                    transform: 'translate(-50%, -50%)',
                    width: '60px',
                    height: '60px',
                    background: 'rgba(255, 107, 107, 0.9)',
                    borderRadius: '50%',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    fontSize: '1.5rem',
                    color: 'white',
                    transition: 'all 0.3s ease',
                  }}>
                    ▶
                  </div>
                  <span style={{
                    position: 'absolute',
                    bottom: '10px',
                    right: '10px',
                    background: 'rgba(0, 0, 0, 0.8)',
                    color: 'white',
                    padding: '4px 8px',
                    borderRadius: '4px',
                    fontSize: '0.8rem',
                  }}>
                    {video.duration}
                  </span>
                </div>
                <h3 style={{ color: 'var(--text-primary)', marginBottom: '0.5rem' }}>
                  {video.title}
                </h3>
                <p style={{ color: 'var(--text-secondary)', marginBottom: '0.5rem', fontSize: '0.95rem' }}>
                  {video.description}
                </p>
                {video.venue && (
                  <p style={{ color: 'var(--accent-color)', marginBottom: '1rem', fontSize: '0.9rem' }}>
                    📍 {video.venue}
                  </p>
                )}
                <div style={{
                  display: 'flex',
                  justifyContent: 'space-between',
                  fontSize: '0.85rem',
                  color: 'var(--text-muted)',
                }}>
                  <span>{video.views} views</span>
                  <span>{video.releaseDate}</span>
                </div>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* Behind the Scenes */}
      <section className="behind-scenes" style={{
        padding: '5rem 0',
        background: 'var(--secondary-color)',
      }}>
        <div className="container">
          <motion.div
            initial={{ opacity: 0, y: 50 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8 }}
            viewport={{ once: true }}
            style={{ textAlign: 'center', marginBottom: '3rem' }}
          >
            <h2 style={{ color: 'var(--text-primary)', marginBottom: '1rem' }}>
              Behind the Scenes
            </h2>
            <p style={{ color: 'var(--text-secondary)', fontSize: '1.1rem' }}>
              Exclusive content from the studio and tour
            </p>
          </motion.div>

          <div style={{
            display: 'grid',
            gridTemplateColumns: 'repeat(auto-fit, minmax(350px, 1fr))',
            gap: '2rem',
          }}>
            {behindTheScenes.map((video, index) => (
              <motion.div
                key={video.id}
                initial={{ opacity: 0, y: 50 }}
                whileInView={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.8, delay: index * 0.1 }}
                viewport={{ once: true }}
                className="card"
                style={{ cursor: 'pointer' }}
                onClick={() => openVideoModal(video)}
              >
                <div style={{ position: 'relative', marginBottom: '1rem' }}>
                  <img
                    src={video.thumbnail}
                    alt={video.title}
                    style={{
                      width: '100%',
                      borderRadius: '10px',
                      display: 'block',
                    }}
                  />
                  <div style={{
                    position: 'absolute',
                    top: '50%',
                    left: '50%',
                    transform: 'translate(-50%, -50%)',
                    width: '60px',
                    height: '60px',
                    background: 'rgba(255, 107, 107, 0.9)',
                    borderRadius: '50%',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    fontSize: '1.5rem',
                    color: 'white',
                    transition: 'all 0.3s ease',
                  }}>
                    ▶
                  </div>
                  <span style={{
                    position: 'absolute',
                    bottom: '10px',
                    right: '10px',
                    background: 'rgba(0, 0, 0, 0.8)',
                    color: 'white',
                    padding: '4px 8px',
                    borderRadius: '4px',
                    fontSize: '0.8rem',
                  }}>
                    {video.duration}
                  </span>
                </div>
                <h3 style={{ color: 'var(--text-primary)', marginBottom: '0.5rem' }}>
                  {video.title}
                </h3>
                <p style={{ color: 'var(--text-secondary)', marginBottom: '1rem', fontSize: '0.95rem' }}>
                  {video.description}
                </p>
                <div style={{
                  display: 'flex',
                  justifyContent: 'space-between',
                  fontSize: '0.85rem',
                  color: 'var(--text-muted)',
                }}>
                  <span>{video.views} views</span>
                  <span>{video.releaseDate}</span>
                </div>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* Video Modal */}
      {selectedVideo && (
        <motion.div
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          onClick={closeModal}
          style={{
            position: 'fixed',
            top: 0,
            left: 0,
            right: 0,
            bottom: 0,
            background: 'rgba(0, 0, 0, 0.9)',
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
              background: 'var(--secondary-color)',
              borderRadius: '15px',
              overflow: 'hidden',
            }}
          >
            <div style={{
              position: 'relative',
              paddingBottom: '56.25%',
              height: 0,
            }}>
              <iframe
                src={`https://www.youtube.com/embed/${selectedVideo.videoId}`}
                title={selectedVideo.title}
                style={{
                  position: 'absolute',
                  top: 0,
                  left: 0,
                  width: '100%',
                  height: '100%',
                  border: 'none',
                }}
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowFullScreen
              />
            </div>
            <div style={{ padding: '1.5rem' }}>
              <h3 style={{ color: 'var(--text-primary)', marginBottom: '0.5rem' }}>
                {selectedVideo.title}
              </h3>
              <p style={{ color: 'var(--text-secondary)', marginBottom: '1rem' }}>
                {selectedVideo.description}
              </p>
              <div style={{
                display: 'flex',
                justifyContent: 'space-between',
                alignItems: 'center',
              }}>
                <div style={{
                  display: 'flex',
                  gap: '2rem',
                  fontSize: '0.9rem',
                  color: 'var(--text-muted)',
                }}>
                  <span>{selectedVideo.views} views</span>
                  <span>{selectedVideo.duration}</span>
                  <span>{selectedVideo.releaseDate}</span>
                </div>
                <button
                  onClick={closeModal}
                  className="btn btn-secondary"
                >
                  Close
                </button>
              </div>
            </div>
          </motion.div>
        </motion.div>
      )}
    </div>
  );
};

export default Videos;
