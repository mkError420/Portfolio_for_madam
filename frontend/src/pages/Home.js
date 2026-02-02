import React from 'react';
import { motion } from 'framer-motion';
import { Link } from 'react-router-dom';
import { demoImages } from '../config/demoImages';

const Home = () => {
  return (
    <div className="home">
      {/* Hero Section */}
      <section className="hero" style={{
        minHeight: '100vh',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        position: 'relative',
        background: 'linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%)',
        overflow: 'hidden',
      }}>
        {/* Background Video/Image Placeholder */}
        <div style={{
          position: 'absolute',
          top: 0,
          left: 0,
          width: '100%',
          height: '100%',
          background: `url("${demoImages.hero}") center/cover`,
          opacity: 0.3,
          zIndex: 1,
        }} />

        <div className="container" style={{ position: 'relative', zIndex: 2 }}>
          <motion.div
            initial={{ opacity: 0, y: 50 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 1 }}
            style={{
              textAlign: 'center',
              maxWidth: '800px',
              margin: '0 auto',
            }}
          >
            <motion.h1
              initial={{ opacity: 0, y: 30 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 1, delay: 0.2 }}
              style={{
                fontFamily: "'Playfair Display', serif",
                fontSize: 'clamp(3rem, 8vw, 5rem)',
                marginBottom: '1rem',
                background: 'var(--gradient-primary)',
                WebkitBackgroundClip: 'text',
                WebkitTextFillColor: 'transparent',
                backgroundClip: 'text',
              }}
            >
              Artist Name
            </motion.h1>

            <motion.p
              initial={{ opacity: 0, y: 30 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 1, delay: 0.4 }}
              style={{
                fontSize: 'clamp(1.2rem, 3vw, 1.5rem)',
                color: 'var(--text-secondary)',
                marginBottom: '2rem',
                lineHeight: 1.6,
              }}
            >
              Soulful melodies that touch the heart and inspire the soul
            </motion.p>

            <motion.div
              initial={{ opacity: 0, y: 30 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 1, delay: 0.6 }}
              style={{
                display: 'flex',
                gap: '1rem',
                justifyContent: 'center',
                flexWrap: 'wrap',
              }}
            >
              <Link to="/music" className="btn btn-primary">
                Listen Now
              </Link>
              <Link to="/videos" className="btn btn-secondary">
                Watch Videos
              </Link>
              <Link to="/contact" className="btn btn-secondary">
                Get in Touch
              </Link>
            </motion.div>
          </motion.div>
        </div>

        {/* Scroll Indicator */}
        <motion.div
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          transition={{ duration: 1, delay: 1 }}
          style={{
            position: 'absolute',
            bottom: '30px',
            left: '50%',
            transform: 'translateX(-50%)',
            zIndex: 2,
          }}
        >
          <motion.div
            animate={{ y: [0, 10, 0] }}
            transition={{ duration: 2, repeat: Infinity }}
            style={{
              width: '30px',
              height: '50px',
              border: '2px solid var(--accent-color)',
              borderRadius: '25px',
              position: 'relative',
            }}
          >
            <motion.div
              animate={{ y: [0, 15, 0] }}
              transition={{ duration: 2, repeat: Infinity }}
              style={{
                width: '4px',
                height: '10px',
                background: 'var(--accent-color)',
                borderRadius: '2px',
                position: 'absolute',
                top: '10px',
                left: '50%',
                transform: 'translateX(-50%)',
              }}
            />
          </motion.div>
        </motion.div>
      </section>

      {/* Latest Release Section */}
      <section className="latest-release" style={{
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
              Latest Release
            </h2>
            <p style={{ color: 'var(--text-secondary)', fontSize: '1.1rem' }}>
              Experience the newest sound from my musical journey
            </p>
          </motion.div>

          <motion.div
            initial={{ opacity: 0, scale: 0.9 }}
            whileInView={{ opacity: 1, scale: 1 }}
            transition={{ duration: 0.8 }}
            viewport={{ once: true }}
            className="card"
            style={{
              display: 'grid',
              gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))',
              gap: '2rem',
              alignItems: 'center',
            }}
          >
            <div>
              <img
                src={demoImages.albums[1]}
                alt="Latest Album"
                style={{
                  width: '100%',
                  maxWidth: '400px',
                  borderRadius: '15px',
                  boxShadow: '0 20px 40px rgba(0, 0, 0, 0.3)',
                }}
              />
            </div>
            <div>
              <h3 style={{ color: 'var(--text-primary)', marginBottom: '1rem' }}>
                Album Title
              </h3>
              <p style={{ color: 'var(--text-secondary)', marginBottom: '1.5rem' }}>
                A collection of songs that represent a new chapter in my musical journey. 
                Each track tells a story, conveying emotions through melody and lyrics.
              </p>
              <div style={{ display: 'flex', gap: '1rem', flexWrap: 'wrap' }}>
                <Link to="/music" className="btn btn-primary">
                  Listen Now
                </Link>
                <button className="btn btn-secondary">
                  Add to Playlist
                </button>
              </div>
            </div>
          </motion.div>
        </div>
      </section>

      {/* Upcoming Events Preview */}
      <section className="events-preview" style={{
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
              Upcoming Events
            </h2>
            <p style={{ color: 'var(--text-secondary)', fontSize: '1.1rem' }}>
              Join me live at these upcoming performances
            </p>
          </motion.div>

          <div style={{
            display: 'grid',
            gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))',
            gap: '2rem',
          }}>
            {[1, 2, 3].map((event) => (
              <motion.div
                key={event}
                initial={{ opacity: 0, y: 50 }}
                whileInView={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.8, delay: event * 0.1 }}
                viewport={{ once: true }}
                className="card"
              >
                <h4 style={{ color: 'var(--text-primary)', marginBottom: '0.5rem' }}>
                  Venue Name {event}
                </h4>
                <p style={{ color: 'var(--accent-color)', marginBottom: '0.5rem' }}>
                  {new Date(Date.now() + event * 7 * 24 * 60 * 60 * 1000).toLocaleDateString('en-US', {
                    month: 'long',
                    day: 'numeric',
                    year: 'numeric'
                  })}
                </p>
                <p style={{ color: 'var(--text-secondary)', marginBottom: '1rem' }}>
                  City, Country
                </p>
                <Link to="/tour" className="btn btn-secondary" style={{ width: '100%' }}>
                  Get Tickets
                </Link>
              </motion.div>
            ))}
          </div>

          <div style={{ textAlign: 'center', marginTop: '2rem' }}>
            <Link to="/tour" className="btn btn-primary">
              View All Events
            </Link>
          </div>
        </div>
      </section>
    </div>
  );
};

export default Home;
