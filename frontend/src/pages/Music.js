import React, { useState, useRef, useEffect } from 'react';
import { motion } from 'framer-motion';

const Music = () => {
  const [currentTrack, setCurrentTrack] = useState(null);
  const [isPlaying, setIsPlaying] = useState(false);
  const [currentTime, setCurrentTime] = useState(0);
  const [duration, setDuration] = useState(0);
  const [volume, setVolume] = useState(0.7);
  const audioRef = useRef(null);

  const albums = [
    {
      id: 1,
      title: "Echoes of Emotion",
      year: "2024",
      cover: "https://via.placeholder.com/300x300/2a2a2a/ffffff?text=Album+1",
      tracks: [
        { id: 1, title: "Whispers of the Soul", duration: "3:45", artist: "Artist Name" },
        { id: 2, title: "Midnight Melodies", duration: "4:12", artist: "Artist Name" },
        { id: 3, title: "Dancing in the Rain", duration: "3:28", artist: "Artist Name" },
        { id: 4, title: "Heart's Symphony", duration: "5:01", artist: "Artist Name" },
        { id: 5, title: "Eternal Echoes", duration: "4:33", artist: "Artist Name" },
      ]
    },
    {
      id: 2,
      title: "Soulful Journey",
      year: "2022",
      cover: "https://via.placeholder.com/300x300/2a2a2a/ffffff?text=Album+2",
      tracks: [
        { id: 6, title: "Journey Begins", duration: "3:15", artist: "Artist Name" },
        { id: 7, title: "Soul's Awakening", duration: "4:45", artist: "Artist Name" },
        { id: 8, title: "Rhythms of Life", duration: "3:52", artist: "Artist Name" },
        { id: 9, title: "Emotional Tides", duration: "4:18", artist: "Artist Name" },
      ]
    },
    {
      id: 3,
      title: "Acoustic Sessions",
      year: "2020",
      cover: "https://via.placeholder.com/300x300/2a2a2a/ffffff?text=Album+3",
      tracks: [
        { id: 10, title: "Unplugged Dreams", duration: "3:08", artist: "Artist Name" },
        { id: 11, title: "Raw Emotions", duration: "4:25", artist: "Artist Name" },
        { id: 12, title: "Intimate Moments", duration: "3:42", artist: "Artist Name" },
      ]
    }
  ];

  const singles = [
    {
      id: 13,
      title: "New Beginning",
      duration: "3:55",
      artist: "Artist Name",
      cover: "https://via.placeholder.com/300x300/2a2a2a/ffffff?text=Single+1",
      releaseDate: "2024"
    },
    {
      id: 14,
      title: "Summer Vibes",
      duration: "3:22",
      artist: "Artist Name",
      cover: "https://via.placeholder.com/300x300/2a2a2a/ffffff?text=Single+2",
      releaseDate: "2024"
    },
    {
      id: 15,
      title: "Winter's Tale",
      duration: "4:08",
      artist: "Artist Name",
      cover: "https://via.placeholder.com/300x300/2a2a2a/ffffff?text=Single+3",
      releaseDate: "2023"
    }
  ];

  useEffect(() => {
    const audio = audioRef.current;
    if (!audio) return;

    const updateTime = () => setCurrentTime(audio.currentTime);
    const updateDuration = () => setDuration(audio.duration);

    audio.addEventListener('timeupdate', updateTime);
    audio.addEventListener('loadedmetadata', updateDuration);

    return () => {
      audio.removeEventListener('timeupdate', updateTime);
      audio.removeEventListener('loadedmetadata', updateDuration);
    };
  }, [currentTrack]);

  const playTrack = (track) => {
    setCurrentTrack(track);
    setIsPlaying(true);
  };

  const togglePlayPause = () => {
    const audio = audioRef.current;
    if (!audio || !currentTrack) return;

    if (isPlaying) {
      audio.pause();
    } else {
      audio.play();
    }
    setIsPlaying(!isPlaying);
  };

  const handleSeek = (e) => {
    const audio = audioRef.current;
    if (!audio) return;
    const newTime = (e.target.value / 100) * duration;
    audio.currentTime = newTime;
    setCurrentTime(newTime);
  };

  const handleVolumeChange = (e) => {
    const audio = audioRef.current;
    if (!audio) return;
    const newVolume = e.target.value / 100;
    audio.volume = newVolume;
    setVolume(newVolume);
  };

  const formatTime = (time) => {
    if (!time || isNaN(time)) return "0:00";
    const minutes = Math.floor(time / 60);
    const seconds = Math.floor(time % 60);
    return `${minutes}:${seconds.toString().padStart(2, '0')}`;
  };

  const allTracks = [
    ...albums.flatMap(album => album.tracks),
    ...singles
  ];

  return (
    <div className="music">
      {/* Hidden Audio Element */}
      <audio
        ref={audioRef}
        src={currentTrack ? `https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3` : ''}
        onEnded={() => setIsPlaying(false)}
      />

      {/* Hero Section */}
      <section className="music-hero" style={{
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
              Music
            </h1>
            <p style={{
              fontSize: '1.2rem',
              color: 'var(--text-secondary)',
              maxWidth: '600px',
              margin: '0 auto',
              lineHeight: 1.6,
            }}>
              Explore my discography, from intimate acoustic sessions to powerful orchestral arrangements
            </p>
          </motion.div>
        </div>
      </section>

      {/* Music Player */}
      {currentTrack && (
        <motion.section
          initial={{ opacity: 0, y: 50 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5 }}
          style={{
            position: 'fixed',
            bottom: 0,
            left: 0,
            right: 0,
            background: 'rgba(26, 26, 26, 0.95)',
            backdropFilter: 'blur(10px)',
            borderTop: '1px solid var(--border-color)',
            padding: '1rem 0',
            zIndex: 1000,
          }}
        >
          <div className="container">
            <div style={{
              display: 'grid',
              gridTemplateColumns: '1fr 2fr 1fr',
              gap: '2rem',
              alignItems: 'center',
            }}>
              {/* Track Info */}
              <div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}>
                <img
                  src="https://via.placeholder.com/50x50/2a2a2a/ffffff?text=Track"
                  alt="Track"
                  style={{
                    width: '50px',
                    height: '50px',
                    borderRadius: '8px',
                  }}
                />
                <div>
                  <h4 style={{ color: 'var(--text-primary)', margin: 0, fontSize: '1rem' }}>
                    {currentTrack.title}
                  </h4>
                  <p style={{ color: 'var(--text-secondary)', margin: 0, fontSize: '0.9rem' }}>
                    {currentTrack.artist}
                  </p>
                </div>
              </div>

              {/* Player Controls */}
              <div>
                <div style={{
                  display: 'flex',
                  alignItems: 'center',
                  gap: '1rem',
                  marginBottom: '0.5rem',
                }}>
                  <button
                    onClick={() => {
                      const currentIndex = allTracks.findIndex(t => t.id === currentTrack.id);
                      const prevIndex = currentIndex > 0 ? currentIndex - 1 : allTracks.length - 1;
                      playTrack(allTracks[prevIndex]);
                    }}
                    style={{
                      background: 'none',
                      border: 'none',
                      color: 'var(--text-primary)',
                      fontSize: '1.2rem',
                      cursor: 'pointer',
                    }}
                  >
                    ⏮
                  </button>
                  <button
                    onClick={togglePlayPause}
                    className="btn btn-primary"
                    style={{
                      width: '40px',
                      height: '40px',
                      borderRadius: '50%',
                      display: 'flex',
                      alignItems: 'center',
                      justifyContent: 'center',
                      padding: 0,
                    }}
                  >
                    {isPlaying ? '⏸' : '▶'}
                  </button>
                  <button
                    onClick={() => {
                      const currentIndex = allTracks.findIndex(t => t.id === currentTrack.id);
                      const nextIndex = currentIndex < allTracks.length - 1 ? currentIndex + 1 : 0;
                      playTrack(allTracks[nextIndex]);
                    }}
                    style={{
                      background: 'none',
                      border: 'none',
                      color: 'var(--text-primary)',
                      fontSize: '1.2rem',
                      cursor: 'pointer',
                    }}
                  >
                    ⏭
                  </button>
                </div>
                <div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}>
                  <span style={{ color: 'var(--text-secondary)', fontSize: '0.8rem' }}>
                    {formatTime(currentTime)}
                  </span>
                  <input
                    type="range"
                    min="0"
                    max="100"
                    value={duration ? (currentTime / duration) * 100 : 0}
                    onChange={handleSeek}
                    style={{
                      flex: 1,
                      height: '4px',
                      background: 'rgba(255, 255, 255, 0.1)',
                      borderRadius: '2px',
                      outline: 'none',
                    }}
                  />
                  <span style={{ color: 'var(--text-secondary)', fontSize: '0.8rem' }}>
                    {formatTime(duration)}
                  </span>
                </div>
              </div>

              {/* Volume Control */}
              <div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}>
                <span style={{ color: 'var(--text-primary)' }}>🔊</span>
                <input
                  type="range"
                  min="0"
                  max="100"
                  value={volume * 100}
                  onChange={handleVolumeChange}
                  style={{
                    width: '80px',
                    height: '4px',
                    background: 'rgba(255, 255, 255, 0.1)',
                    borderRadius: '2px',
                    outline: 'none',
                  }}
                />
              </div>
            </div>
          </div>
        </motion.section>
      )}

      {/* Albums Section */}
      <section className="albums" style={{
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
              Albums
            </h2>
            <p style={{ color: 'var(--text-secondary)', fontSize: '1.1rem' }}>
              Complete collections of musical stories
            </p>
          </motion.div>

          <div style={{
            display: 'grid',
            gridTemplateColumns: 'repeat(auto-fit, minmax(350px, 1fr))',
            gap: '2rem',
          }}>
            {albums.map((album, index) => (
              <motion.div
                key={album.id}
                initial={{ opacity: 0, y: 50 }}
                whileInView={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.8, delay: index * 0.1 }}
                viewport={{ once: true }}
                className="card"
              >
                <div style={{ display: 'flex', gap: '1.5rem', marginBottom: '1.5rem' }}>
                  <img
                    src={album.cover}
                    alt={album.title}
                    style={{
                      width: '120px',
                      height: '120px',
                      borderRadius: '10px',
                      objectFit: 'cover',
                    }}
                  />
                  <div>
                    <h3 style={{ color: 'var(--text-primary)', margin: '0 0 0.5rem' }}>
                      {album.title}
                    </h3>
                    <p style={{ color: 'var(--text-secondary)', margin: '0 0 0.5rem' }}>
                      {album.year} • {album.tracks.length} tracks
                    </p>
                    <button className="btn btn-primary" style={{ fontSize: '0.9rem' }}>
                      Play Album
                    </button>
                  </div>
                </div>

                <div style={{ borderTop: '1px solid var(--border-color)', paddingTop: '1rem' }}>
                  {album.tracks.map((track, trackIndex) => (
                    <div
                      key={track.id}
                      style={{
                        display: 'flex',
                        justifyContent: 'space-between',
                        alignItems: 'center',
                        padding: '0.5rem 0',
                        cursor: 'pointer',
                        borderRadius: '8px',
                        padding: '0.5rem',
                        transition: 'background 0.3s ease',
                      }}
                      onMouseEnter={(e) => {
                        e.currentTarget.style.background = 'rgba(255, 255, 255, 0.05)';
                      }}
                      onMouseLeave={(e) => {
                        e.currentTarget.style.background = 'transparent';
                      }}
                      onClick={() => playTrack(track)}
                    >
                      <div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}>
                        <span style={{ color: 'var(--text-muted)', fontSize: '0.9rem' }}>
                          {trackIndex + 1}
                        </span>
                        <div>
                          <p style={{ 
                            color: currentTrack?.id === track.id ? 'var(--accent-color)' : 'var(--text-primary)', 
                            margin: 0,
                            fontSize: '0.95rem'
                          }}>
                            {track.title}
                          </p>
                          <p style={{ color: 'var(--text-muted)', margin: 0, fontSize: '0.85rem' }}>
                            {track.artist}
                          </p>
                        </div>
                      </div>
                      <div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}>
                        <span style={{ color: 'var(--text-muted)', fontSize: '0.9rem' }}>
                          {track.duration}
                        </span>
                        <button
                          onClick={(e) => {
                            e.stopPropagation();
                            playTrack(track);
                          }}
                          style={{
                            background: 'none',
                            border: 'none',
                            color: currentTrack?.id === track.id && isPlaying ? 'var(--accent-color)' : 'var(--text-secondary)',
                            fontSize: '1rem',
                            cursor: 'pointer',
                          }}
                        >
                          {currentTrack?.id === track.id && isPlaying ? '⏸' : '▶'}
                        </button>
                      </div>
                    </div>
                  ))}
                </div>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* Singles Section */}
      <section className="singles" style={{
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
              Singles
            </h2>
            <p style={{ color: 'var(--text-secondary)', fontSize: '1.1rem' }}>
              Standalone releases and special tracks
            </p>
          </motion.div>

          <div style={{
            display: 'grid',
            gridTemplateColumns: 'repeat(auto-fit, minmax(250px, 1fr))',
            gap: '2rem',
          }}>
            {singles.map((single, index) => (
              <motion.div
                key={single.id}
                initial={{ opacity: 0, scale: 0.9 }}
                whileInView={{ opacity: 1, scale: 1 }}
                transition={{ duration: 0.8, delay: index * 0.1 }}
                viewport={{ once: true }}
                className="card"
                style={{ textAlign: 'center' }}
              >
                <img
                  src={single.cover}
                  alt={single.title}
                  style={{
                    width: '100%',
                    borderRadius: '10px',
                    marginBottom: '1rem',
                  }}
                />
                <h3 style={{ color: 'var(--text-primary)', margin: '0.5rem 0' }}>
                  {single.title}
                </h3>
                <p style={{ color: 'var(--text-secondary)', margin: '0.5rem 0', fontSize: '0.9rem' }}>
                  {single.releaseDate} • {single.duration}
                </p>
                <button
                  onClick={() => playTrack(single)}
                  className="btn btn-primary"
                  style={{ width: '100%' }}
                >
                  {currentTrack?.id === single.id && isPlaying ? '⏸ Playing' : '▶ Play'}
                </button>
              </motion.div>
            ))}
          </div>
        </div>
      </section>
    </div>
  );
};

export default Music;
