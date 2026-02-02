import React from 'react';
import { motion } from 'framer-motion';

const About = () => {
  const timeline = [
    {
      year: '2015',
      title: 'Musical Journey Begins',
      description: 'Started formal vocal training and discovered my passion for soulful melodies.',
      type: 'milestone'
    },
    {
      year: '2017',
      title: 'First Live Performance',
      description: 'Performed at local venues and gained recognition for unique vocal style.',
      type: 'performance'
    },
    {
      year: '2019',
      title: 'Debut Single Release',
      description: 'Released first single "Whispers of the Soul" which received critical acclaim.',
      type: 'release'
    },
    {
      year: '2020',
      title: 'First Album',
      description: 'Launched debut album "Echoes of Emotion" featuring 12 original tracks.',
      type: 'album'
    },
    {
      year: '2022',
      title: 'International Tour',
      description: 'Embarked on first international tour, performing in 15 countries.',
      type: 'tour'
    },
    {
      year: '2024',
      title: 'Latest Achievement',
      description: 'Received prestigious music award and collaborated with renowned artists.',
      type: 'award'
    }
  ];

  const skills = [
    { name: 'Vocal Performance', level: 95 },
    { name: 'Songwriting', level: 85 },
    { name: 'Music Production', level: 75 },
    { name: 'Live Performance', level: 90 },
    { name: 'Music Theory', level: 80 }
  ];

  return (
    <div className="about">
      {/* Hero Section */}
      <section className="about-hero" style={{
        padding: '8rem 0 4rem',
        background: 'linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%)',
        position: 'relative',
        overflow: 'hidden',
      }}>
        <div className="container">
          <motion.div
            initial={{ opacity: 0, y: 50 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8 }}
            style={{
              display: 'grid',
              gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))',
              gap: '3rem',
              alignItems: 'center',
            }}
          >
            <div>
              <motion.h1
                initial={{ opacity: 0, x: -50 }}
                animate={{ opacity: 1, x: 0 }}
                transition={{ duration: 0.8, delay: 0.2 }}
                style={{
                  fontFamily: "'Playfair Display', serif",
                  fontSize: 'clamp(2.5rem, 5vw, 3.5rem)',
                  color: 'var(--text-primary)',
                  marginBottom: '1.5rem',
                }}
              >
                About Me
              </motion.h1>
              <motion.p
                initial={{ opacity: 0, x: -50 }}
                animate={{ opacity: 1, x: 0 }}
                transition={{ duration: 0.8, delay: 0.4 }}
                style={{
                  fontSize: '1.1rem',
                  lineHeight: 1.8,
                  color: 'var(--text-secondary)',
                  marginBottom: '1.5rem',
                }}
              >
                Music has been my lifelong passion and my medium for expressing the deepest emotions of the human experience. 
                From intimate acoustic sessions to grand stadium performances, every note I sing comes from the heart.
              </motion.p>
              <motion.p
                initial={{ opacity: 0, x: -50 }}
                animate={{ opacity: 1, x: 0 }}
                transition={{ duration: 0.8, delay: 0.6 }}
                style={{
                  fontSize: '1.1rem',
                  lineHeight: 1.8,
                  color: 'var(--text-secondary)',
                }}
              >
                My journey in music has been one of constant growth, learning, and discovery. 
                I believe in the power of melody to heal, inspire, and connect people across all boundaries.
              </motion.p>
            </div>
            <motion.div
              initial={{ opacity: 0, scale: 0.8 }}
              animate={{ opacity: 1, scale: 1 }}
              transition={{ duration: 0.8, delay: 0.4 }}
              style={{ textAlign: 'center' }}
            >
              <img
                src="https://via.placeholder.com/400x500/2a2a2a/ffffff?text=Artist+Photo"
                alt="Artist"
                style={{
                  width: '100%',
                  maxWidth: '400px',
                  borderRadius: '20px',
                  boxShadow: '0 30px 60px rgba(0, 0, 0, 0.4)',
                  border: '3px solid var(--accent-color)',
                }}
              />
            </motion.div>
          </motion.div>
        </div>
      </section>

      {/* Timeline Section */}
      <section className="timeline" style={{
        padding: '5rem 0',
        background: 'var(--primary-color)',
      }}>
        <div className="container">
          <motion.div
            initial={{ opacity: 0, y: 50 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8 }}
            viewport={{ once: true }}
            style={{ textAlign: 'center', marginBottom: '4rem' }}
          >
            <h2 style={{ color: 'var(--text-primary)', marginBottom: '1rem' }}>
              My Musical Journey
            </h2>
            <p style={{ color: 'var(--text-secondary)', fontSize: '1.1rem' }}>
              Key moments that shaped my career
            </p>
          </motion.div>

          <div style={{ position: 'relative' }}>
            {/* Timeline Line */}
            <div style={{
              position: 'absolute',
              left: '50%',
              top: 0,
              bottom: 0,
              width: '2px',
              background: 'var(--accent-color)',
              transform: 'translateX(-50%)',
            }} />

            {timeline.map((item, index) => (
              <motion.div
                key={index}
                initial={{ opacity: 0, x: index % 2 === 0 ? -50 : 50 }}
                whileInView={{ opacity: 1, x: 0 }}
                transition={{ duration: 0.8, delay: index * 0.1 }}
                viewport={{ once: true }}
                style={{
                  display: 'flex',
                  justifyContent: index % 2 === 0 ? 'flex-end' : 'flex-start',
                  marginBottom: '3rem',
                  position: 'relative',
                }}
              >
                <div style={{
                  width: '45%',
                  padding: index % 2 === 0 ? '0 2rem 0 0' : '0 0 0 2rem',
                  textAlign: index % 2 === 0 ? 'right' : 'left',
                }}>
                  <div className="card" style={{
                    background: 'var(--card-bg)',
                    border: '1px solid var(--border-color)',
                    borderRadius: '15px',
                    padding: '2rem',
                    position: 'relative',
                  }}>
                    <div style={{
                      position: 'absolute',
                      top: '50%',
                      [index % 2 === 0 ? 'right' : 'left']: '-10px',
                      transform: 'translateY(-50%)',
                      width: '0',
                      height: '0',
                      borderTop: '10px solid transparent',
                      borderBottom: '10px solid transparent',
                      [index % 2 === 0 ? 'borderLeft' : 'borderRight']: '10px solid var(--card-bg)',
                    }} />
                    <span style={{
                      color: 'var(--accent-color)',
                      fontSize: '0.9rem',
                      fontWeight: 600,
                      textTransform: 'uppercase',
                      letterSpacing: '1px',
                    }}>
                      {item.year}
                    </span>
                    <h3 style={{
                      color: 'var(--text-primary)',
                      margin: '0.5rem 0',
                      fontSize: '1.3rem',
                    }}>
                      {item.title}
                    </h3>
                    <p style={{ color: 'var(--text-secondary)', lineHeight: 1.6 }}>
                      {item.description}
                    </p>
                  </div>
                </div>

                {/* Timeline Dot */}
                <div style={{
                  position: 'absolute',
                  left: '50%',
                  top: '2rem',
                  transform: 'translateX(-50%)',
                  width: '20px',
                  height: '20px',
                  background: 'var(--accent-color)',
                  borderRadius: '50%',
                  border: '4px solid var(--primary-color)',
                  zIndex: 1,
                }} />
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* Skills Section */}
      <section className="skills" style={{
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
              Musical Skills & Expertise
            </h2>
            <p style={{ color: 'var(--text-secondary)', fontSize: '1.1rem' }}>
              Years of dedication to musical excellence
            </p>
          </motion.div>

          <div style={{
            display: 'grid',
            gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))',
            gap: '2rem',
          }}>
            {skills.map((skill, index) => (
              <motion.div
                key={index}
                initial={{ opacity: 0, x: 50 }}
                whileInView={{ opacity: 1, x: 0 }}
                transition={{ duration: 0.8, delay: index * 0.1 }}
                viewport={{ once: true }}
                className="card"
              >
                <div style={{ marginBottom: '1rem' }}>
                  <div style={{
                    display: 'flex',
                    justifyContent: 'space-between',
                    alignItems: 'center',
                    marginBottom: '0.5rem',
                  }}>
                    <h4 style={{ color: 'var(--text-primary)', margin: 0 }}>
                      {skill.name}
                    </h4>
                    <span style={{ color: 'var(--accent-color)', fontWeight: 600 }}>
                      {skill.level}%
                    </span>
                  </div>
                  <div style={{
                    width: '100%',
                    height: '8px',
                    background: 'rgba(255, 255, 255, 0.1)',
                    borderRadius: '4px',
                    overflow: 'hidden',
                  }}>
                    <motion.div
                      initial={{ width: 0 }}
                      whileInView={{ width: `${skill.level}%` }}
                      transition={{ duration: 1, delay: index * 0.1 }}
                      viewport={{ once: true }}
                      style={{
                        height: '100%',
                        background: 'var(--gradient-primary)',
                        borderRadius: '4px',
                      }}
                    />
                  </div>
                </div>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* Personal Message */}
      <section className="personal-message" style={{
        padding: '5rem 0',
        background: 'var(--primary-color)',
      }}>
        <div className="container">
          <motion.div
            initial={{ opacity: 0, y: 50 }}
            whileInView={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8 }}
            viewport={{ once: true }}
            className="card"
            style={{
              textAlign: 'center',
              padding: '3rem',
              maxWidth: '800px',
              margin: '0 auto',
            }}
          >
            <h2 style={{ color: 'var(--text-primary)', marginBottom: '1.5rem' }}>
              A Message to My Fans
            </h2>
            <p style={{
              fontSize: '1.1rem',
              lineHeight: 1.8,
              color: 'var(--text-secondary)',
              marginBottom: '1.5rem',
            }}>
              Thank you for being part of this incredible journey. Your support and love for music 
              inspire me to keep creating and sharing my art with the world. Every song I write 
              and every performance I give is dedicated to you - the listeners who make this all possible.
            </p>
            <p style={{
              fontSize: '1.1rem',
              lineHeight: 1.8,
              color: 'var(--text-secondary)',
            }}>
              Music is a universal language that connects us all, and I'm honored to be your vessel 
              for these melodies and emotions. Let's continue to share the beauty of music together.
            </p>
          </motion.div>
        </div>
      </section>
    </div>
  );
};

export default About;
