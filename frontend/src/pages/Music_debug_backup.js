import React, { useState, useRef, useEffect } from 'react';
import { motion } from 'framer-motion';

const Music = () => {
  const [currentTrack, setCurrentTrack] = useState(null);
  const [isPlaying, setIsPlaying] = useState(false);
  const [currentTime, setCurrentTime] = useState(0);
  const [duration, setDuration] = useState(0);
  const [volume, setVolume] = useState(0.7);
  const [albums, setAlbums] = useState([]);
  const [singles, setSingles] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [debugInfo, setDebugInfo] = useState(null);
  const audioRef = useRef(null);

  // Fetch music data from API with debugging
  useEffect(() => {
    const fetchMusicData = async () => {
      try {
        setLoading(true);
        setError(null);
        setDebugInfo(null);
        
        console.log('🎵 Starting API fetch...');
        const response = await fetch('http://localhost/Portfolio/backend/api/music.php');
        
        console.log('📡 Response status:', response.status);
        console.log('📡 Response headers:', response.headers);
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('📊 API Response:', data);
        
        // Store debug info
        setDebugInfo({
          status: response.status,
          success: data.success,
          albumsCount: data.data?.albums_only?.length || 0,
          singlesCount: data.data?.singles?.length || 0,
          fullResponse: data
        });
        
        if (data.success) {
          const albumsData = data.data.albums_only || [];
          const singlesData = data.data.singles || [];
          
          console.log('📀 Albums received:', albumsData);
          console.log('🎤 Singles received:', singlesData);
          
          setAlbums(albumsData);
          setSingles(singlesData);
          
          if (albumsData.length === 0 && singlesData.length === 0) {
            setError('No music data available in database');
          }
        } else {
          setError('API Error: ' + (data.error || 'Unknown error'));
        }
      } catch (err) {
        console.error('❌ Fetch error:', err);
        setError('Failed to load music data: ' + err.message);
        setDebugInfo({
          error: err.message,
          stack: err.stack
        });
      } finally {
        setLoading(false);
      }
    };

    fetchMusicData();
  }, []);

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
    ...(Array.isArray(albums) ? albums.flatMap(album => album.tracks || []) : []),
    ...(Array.isArray(singles) ? singles : [])
  ];

  return (
    <div className="music">
      {/* Debug Panel */}
      <div style={{
        position: 'fixed',
        top: '10px',
        right: '10px',
        background: 'rgba(0,0,0,0.8)',
        color: 'white',
        padding: '15px',
        borderRadius: '8px',
        fontSize: '12px',
        maxWidth: '300px',
        zIndex: 9999
      }}>
        <h4>🔍 Debug Info</h4>
        <p><strong>Loading:</strong> {loading ? 'Yes' : 'No'}</p>
        <p><strong>Error:</strong> {error || 'None'}</p>
        <p><strong>Albums:</strong> {albums.length}</p>
        <p><strong>Singles:</strong> {singles.length}</p>
        {debugInfo && (
          <div style={{ marginTop: '10px' }}>
            <p><strong>API Status:</strong> {debugInfo.status}</p>
            <p><strong>Success:</strong> {debugInfo.success ? 'Yes' : 'No'}</p>
            <details>
              <summary>Full Debug</summary>
              <pre style={{ fontSize: '10px', overflow: 'auto' }}>
                {JSON.stringify(debugInfo, null, 2)}
              </pre>
            </details>
          </div>
        )}
      </div>

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
              Music (Debug Mode)
            </h1>
            <p style={{
              fontSize: '1.2rem',
              color: 'var(--text-secondary)',
              maxWidth: '600px',
              margin: '0 auto',
              lineHeight: 1.6,
            }}>
              Debug version - check the debug panel in top-right corner
            </p>
          </motion.div>
        </div>
      </section>

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

          {loading ? (
            <div style={{ textAlign: 'center', padding: '3rem' }}>
              <p style={{ color: 'var(--text-secondary)' }}>Loading albums...</p>
            </div>
          ) : error ? (
            <div style={{ textAlign: 'center', padding: '3rem' }}>
              <p style={{ color: 'var(--accent-color)' }}>{error}</p>
              <button onClick={() => window.location.reload()} style={{
                background: '#007bff',
                color: 'white',
                padding: '10px 20px',
                border: 'none',
                borderRadius: '5px',
                marginTop: '10px'
              }}>
                Retry
              </button>
            </div>
          ) : albums.length === 0 ? (
            <div style={{ textAlign: 'center', padding: '3rem' }}>
              <p style={{ color: 'var(--text-secondary)' }}>No albums available yet.</p>
            </div>
          ) : (
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
                        {album.year} • {album.tracks ? album.tracks.length : 0} tracks
                      </p>
                      <button className="btn btn-primary" style={{ fontSize: '0.9rem' }}>
                        Play Album
                      </button>
                    </div>
                  </div>
                </motion.div>
              ))}
            </div>
          )}
        </div>
      </section>
    </div>
  );
};

export default Music;
