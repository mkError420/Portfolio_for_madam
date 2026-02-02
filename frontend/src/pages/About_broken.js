import React from 'react';
import { motion } from 'framer-motion';

const About = () => {
  return (
    <div className="about">
        {/* Hero Section with Black Background */}
        <section className="about-hero" style={{
          position: 'relative',
          minHeight: '100vh',
          background: 'linear-gradient(135deg, #000000 0%, #0a0a0a 25%, #141414 50%, #1a1a1a 75%, #0f0f0f 100%)',
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'center',
          overflow: 'hidden',
        }}>
          {/* Animated Background Elements */}
          <div style={{
            position: 'absolute',
            width: '100%',
            height: '100%',
            overflow: 'hidden',
          }}>
            {/* Dark floating elements */}
            {[...Array(15)].map((_, i) => (
              <motion.div
                key={i}
                style={{
                  position: 'absolute',
                  width: Math.random() * 80 + 40 + 'px',
                  height: Math.random() * 80 + 40 + 'px',
                  background: `rgba(255, 255, 255, ${Math.random() * 0.02 + 0.01})`,
                  borderRadius: '50%',
                  left: Math.random() * 100 + '%',
                  top: Math.random() * 100 + '%',
                  filter: 'blur(1px)',
                }}
                animate={{
                  x: [0, Math.random() * 100 - 50],
                  y: [0, Math.random() * 100 - 50],
                  scale: [1, Math.random() * 0.3 + 0.8, 1],
                  opacity: [0.1, 0.3, 0.1],
                }}
                transition={{
                  duration: Math.random() * 15 + 10,
                  repeat: Infinity,
                  repeatType: 'reverse',
                  ease: 'easeInOut',
                }}
              />
            ))}
            
            {/* Subtle gradient overlays */}
            <div style={{
              position: 'absolute',
              top: 0,
              left: 0,
              right: 0,
              height: '50%',
              background: 'linear-gradient(180deg, rgba(102, 126, 234, 0.05) 0%, transparent 100%)',
            }} />
            
            <div style={{
              position: 'absolute',
              bottom: 0,
              left: 0,
              right: 0,
              height: '50%',
              background: 'linear-gradient(0deg, rgba(118, 75, 162, 0.05) 0%, transparent 100%)',
            }} />
          </div>

          <div className="container" style={{ position: 'relative', zIndex: 2 }}>
            <motion.div
              initial={{ opacity: 0, y: 100 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 1.2 }}
              style={{ textAlign: 'center' }}
            >
              <motion.div
                initial={{ scale: 0.8, opacity: 0 }}
                animate={{ scale: 1, opacity: 1 }}
                transition={{ duration: 1, delay: 0.3 }}
                className="avatar-circle"
                style={{
                  width: '250px',
                  height: '250px',
                  margin: '0 auto 2rem',
                  background: 'linear-gradient(45deg, #1a1a1a, #2a2a2a, #1a1a1a)',
                  borderRadius: '50%',
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  boxShadow: '0 20px 60px rgba(0,0,0,0.8), 0 0 100px rgba(102, 126, 234, 0.1)',
                  position: 'relative',
                  border: '2px solid rgba(255, 255, 255, 0.1)',
                }}
              >
                <div style={{
                  width: '90%',
                  height: '90%',
                  background: 'linear-gradient(135deg, #0f0f0f, #1a1a1a)',
                  borderRadius: '50%',
                  display: 'flex',
                  alignItems: 'center',
                  justifyContent: 'center',
                  fontSize: '4rem',
                  background: 'linear-gradient(135deg, #667eea, #764ba2)',
                  WebkitBackgroundClip: 'text',
                  WebkitTextFillColor: 'transparent',
                  fontWeight: 'bold',
                  boxShadow: 'inset 0 0 30px rgba(0,0,0,0.5)',
                }}>
                  A
                </div>
                
                {/* Subtle ring animation */}
                <motion.div
                  animate={{
                    rotate: 360,
                  }}
                  transition={{
                    duration: 20,
                    repeat: Infinity,
                    ease: 'linear',
                  }}
                  style={{
                    position: 'absolute',
                    width: '100%',
                    height: '100%',
                    borderRadius: '50%',
                    border: '1px solid transparent',
                    borderTopColor: 'rgba(102, 126, 234, 0.3)',
                    borderRightColor: 'rgba(118, 75, 162, 0.3)',
                  }}
                />
              </motion.div>
              
              <motion.h1
                initial={{ opacity: 0, y: 50 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 1, delay: 0.5 }}
                className="about-hero-title"
                style={{
                  fontFamily: "'Playfair Display', serif",
                  fontSize: 'clamp(3rem, 8vw, 6rem)',
                  color: 'white',
                  marginBottom: '1rem',
                  textShadow: '0 10px 30px rgba(0,0,0,0.3)',
                  letterSpacing: '-2px',
                }}
              >
                About Me
              </motion.h1>
              
              <motion.p
                initial={{ opacity: 0, y: 30 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 1, delay: 0.7 }}
                style={{
                  fontSize: '1.3rem',
                  color: 'rgba(255,255,255,0.9)',
                  maxWidth: '600px',
                  margin: '0 auto',
                  lineHeight: 1.6,
                  textShadow: '0 5px 15px rgba(0,0,0,0.3)',
                }}
              >
                Musician • Artist • Storyteller
              </motion.p>
            </motion.div>
          </div>

          {/* Scroll Indicator */}
          <motion.div
            animate={{ y: [0, 10, 0] }}
            transition={{ duration: 2, repeat: Infinity }}
            style={{
              position: 'absolute',
              bottom: '30px',
              left: '50%',
              transform: 'translateX(-50%)',
              color: 'rgba(255, 255, 255, 0.6)',
              fontSize: '2rem',
              textShadow: '0 0 20px rgba(102, 126, 234, 0.5)',
            }}
          >
            ↓
          </motion.div>
        </section>

        {/* Story Section */}
        <section className="story-section" style={{
          padding: '6rem 0',
          background: 'linear-gradient(180deg, #0f0f0f 0%, #1a1a1a 50%, #252525 100%)',
          position: 'relative',
        }}>
          <div className="container">
            <motion.div
              initial={{ opacity: 0, x: -100 }}
              whileInView={{ opacity: 1, x: 0 }}
              transition={{ duration: 1 }}
              viewport={{ once: true }}
              className="story-grid"
              style={{
                display: 'grid',
                gridTemplateColumns: '1fr 1fr',
                gap: '4rem',
                alignItems: 'center',
              }}
            >
              <div>
                <motion.h2
                  initial={{ opacity: 0, y: 50 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.8 }}
                  viewport={{ once: true }}
                  style={{
                    fontFamily: "'Playfair Display', serif",
                    fontSize: '3rem',
                    color: 'var(--text-primary)',
                    marginBottom: '2rem',
                    position: 'relative',
                  }}
                >
                  My Story
                  <motion.div
                    initial={{ width: 0 }}
                    whileInView={{ width: '100px' }}
                    transition={{ duration: 1, delay: 0.5 }}
                    viewport={{ once: true }}
                    style={{
                      height: '4px',
                      background: 'linear-gradient(90deg, #667eea, #764ba2)',
                      marginTop: '1rem',
                    }}
                  />
                </motion.h2>
                
                <motion.p
                  initial={{ opacity: 0, y: 30 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.8, delay: 0.2 }}
                  viewport={{ once: true }}
                  style={{
                    fontSize: '1.1rem',
                    color: 'var(--text-secondary)',
                    lineHeight: 1.8,
                    marginBottom: '1.5rem',
                  }}
                >
                  From humble beginnings in a small town to international stages, my journey has been defined by passion, perseverance, and the universal language of music. Every note I play carries the stories of my experiences, the dreams that fueled my journey, and the connections I've made with audiences around the world.
                </motion.p>
                
                <motion.p
                  initial={{ opacity: 0, y: 30 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.8, delay: 0.4 }}
                  viewport={{ once: true }}
                  style={{
                    fontSize: '1.1rem',
                    color: 'var(--text-secondary)',
                    lineHeight: 1.8,
                  }}
                >
                  Music isn't just what I do—it's who I am. It's the voice I found when words weren't enough, the comfort I discovered in melodies, and the bridge I built to connect hearts across cultures and continents.
                </motion.p>
              </div>
              
              <motion.div
                initial={{ opacity: 0, scale: 0.8 }}
                whileInView={{ opacity: 1, scale: 1 }}
                transition={{ duration: 1, delay: 0.3 }}
                viewport={{ once: true }}
                style={{
                  position: 'relative',
                }}
              >
                <style jsx>{`
                  /* Enhanced Responsive Design */
                  
                  /* Hero Section */
                  @media (max-width: 1024px) {
                    .about-hero h1 {
                      font-size: 4rem;
                    }
                    
                    .avatar-circle {
                      width: 200px;
                      height: 200px;
                    }
                  }
                  
                  @media (max-width: 768px) {
                    .about-hero h1 {
                      font-size: 3rem;
                    }
                    
                    .avatar-circle {
                      width: 180px;
                      height: 180px;
                    }
                  }
                  
                  /* Story Section */
                  @media (max-width: 1024px) {
                    .story-grid {
                      grid-template-columns: 1fr;
                      gap: 2rem;
                    }
                    
                    .story-image {
                      height: 300px;
                    }
                  }
                  
                  @media (max-width: 768px) {
                    .story-grid {
                      grid-template-columns: 1fr;
                    }
                    
                    .story-image {
                      height: 250px;
                      margin-top: 2rem;
                    }
                  }
                  
                  /* Philosophy Section */
                  @media (max-width: 1024px) {
                    .philosophy-grid {
                      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                      gap: 2rem;
                    }
                  }
                  
                  @media (max-width: 768px) {
                    .philosophy-grid {
                      grid-template-columns: 1fr;
                      gap: 1.5rem;
                    }
                  }
                  
                  /* Journey Section */
                  @media (max-width: 1024px) {
                    .journey-grid {
                      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                      gap: 2rem;
                    }
                  }
                  
                  @media (max-width: 768px) {
                    .journey-grid {
                      grid-template-columns: 1fr;
                      gap: 1.5rem;
                    }
                    
                    .journey-card {
                      padding: 2rem;
                    }
                  }
                  
                  /* CTA Section */
                  @media (max-width: 1024px) {
                    .cta-section h2 {
                      font-size: 2.5rem;
                    }
                  }
                  
                  @media (max-width: 768px) {
                    .cta-section h2 {
                      font-size: 2rem;
                    }
                    
                    .cta-button {
                      width: 100%;
                      max-width: 300px;
                    }
                  }
                `}</style>
                <div style={{
                  width: '100%',
                  height: '400px',
                  <div style={{
                    position: 'absolute',
                    inset: 0,
                    background: 'url("https://images.unsplash.com/photo-1514320291840-2e0a9bf2a9ae?w=800&h=600&fit=crop") center/cover',
                    opacity: 0.3,
                  }} />
                  <div style={{
                    position: 'absolute',
                    inset: 0,
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    fontSize: '6rem',
                    color: 'white',
                    opacity: 0.8,
                  }}>
                    🎵
                  </div>
                </div>
              </motion.div>
            </motion.div>
          </div>
        </section>

        {/* Philosophy Section */}
        <section className="philosophy-section" style={{
          padding: '6rem 0',
          background: 'linear-gradient(135deg, #1a1a1a 0%, #0f0f0f 50%, #1a1a1a 100%)',
          position: 'relative',
        }}>
          <div className="container">
            <motion.div
              initial={{ opacity: 0, y: 50 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ duration: 1 }}
              viewport={{ once: true }}
              style={{ textAlign: 'center', marginBottom: '4rem' }}
            >
              <h2 style={{
                fontFamily: "'Playfair Display', serif",
                fontSize: '3rem',
                color: 'var(--text-primary)',
                marginBottom: '1rem',
              }}>
                Musical Philosophy
              </h2>
              <p style={{
                fontSize: '1.2rem',
                color: 'var(--text-secondary)',
                maxWidth: '600px',
                margin: '0 auto',
              }}>
                The principles that guide every note I compose and every performance I give
              </p>
            </motion.div>

            <div style={{
              display: 'grid',
              gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))',
              gap: '3rem',
            }}>
              {[
                {
                  icon: '🎭',
                  title: 'Authenticity',
                  description: 'Every performance is a genuine expression of emotion and experience, never just a technical exercise.'
                },
                {
                  icon: '🌍',
                  title: 'Connection',
                  description: 'Music is the universal language that transcends barriers and brings people together in shared experience.'
                },
                {
                  icon: '✨',
                  title: 'Innovation',
                  description: 'Pushing boundaries while honoring traditions, creating something uniquely mine yet universally relatable.'
                }
              ].map((item, index) => (
                <motion.div
                  key={index}
                  initial={{ opacity: 0, y: 50 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  transition={{ duration: 0.8, delay: index * 0.2 }}
                  viewport={{ once: true }}
                  style={{
                    background: 'linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1))',
                    padding: '2.5rem',
                    borderRadius: '20px',
                    textAlign: 'center',
                    border: '1px solid rgba(102, 126, 234, 0.2)',
                    backdropFilter: 'blur(10px)',
                  }}
                >
                  <div style={{
                    fontSize: '3rem',
                    marginBottom: '1rem',
                  }}>
                    {item.icon}
                  </div>
                  <h3 style={{
                    fontSize: '1.5rem',
                    color: 'var(--text-primary)',
                    marginBottom: '1rem',
                  }}>
                    {item.title}
                  </h3>
                  <p style={{
                    color: 'var(--text-secondary)',
                    lineHeight: 1.6,
                  }}>
                    {item.description}
                  </p>
                </motion.div>
              ))}
            </div>
          </div>
        </section>

        {/* Musical Journey Section - Unique Design */}
        <section className="journey-section" style={{
          padding: '8rem 0',
          background: 'linear-gradient(180deg, #0f0f0f 0%, #1a1a1a 50%, #0f0f0f 100%)',
          position: 'relative',
          overflow: 'hidden',
        }}>
          {/* Background Pattern */}
          <div style={{
            position: 'absolute',
            inset: 0,
            backgroundImage: `radial-gradient(circle at 20% 50%, rgba(102, 126, 234, 0.1) 0%, transparent 50%),
                             radial-gradient(circle at 80% 50%, rgba(118, 75, 162, 0.1) 0%, transparent 50%)`,
          }} />

          <div className="container" style={{ position: 'relative', zIndex: 2 }}>
            <motion.div
              initial={{ opacity: 0, y: 50 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ duration: 1 }}
              viewport={{ once: true }}
              style={{ textAlign: 'center', marginBottom: '5rem' }}
            >
              <h2 style={{
                fontFamily: "'Playfair Display', serif",
                fontSize: '3.5rem',
                color: 'var(--text-primary)',
                marginBottom: '1rem',
                background: 'linear-gradient(135deg, #667eea, #764ba2, #f093fb)',
                WebkitBackgroundClip: 'text',
                WebkitTextFillColor: 'transparent',
                backgroundClip: 'text',
              }}>
                Musical Journey
              </h2>
              <p style={{
                fontSize: '1.2rem',
                color: 'var(--text-secondary)',
                maxWidth: '600px',
                margin: '0 auto',
              }}>
                The path that shaped my musical identity
              </p>
            </motion.div>

            {/* Unique Journey Cards Layout */}
            <div style={{
              display: 'grid',
              gridTemplateColumns: 'repeat(auto-fit, minmax(280px, 1fr))',
              gap: '2rem',
              position: 'relative',
            }}>
              {[
                {
                  year: '2015',
                  title: 'The Beginning',
                  description: 'First guitar lesson and the discovery of a lifelong passion for music.',
                  icon: '🎸',
                  color: '#667eea',
                  delay: 0
                },
                {
                  year: '2018',
                  title: 'First Performance',
                  description: 'Debut at local venue, received standing ovation and knew this was my calling.',
                  icon: '🎤',
                  color: '#764ba2',
                  delay: 0.2
                },
                {
                  year: '2020',
                  title: 'Breakthrough Album',
                  description: 'Released first studio album, topped indie charts and reached global audience.',
                  icon: '💿',
                  color: '#f093fb',
                  delay: 0.4
                },
                {
                  year: '2023',
                  title: 'World Tour',
                  description: 'Performed in 15 countries, connected with diverse audiences worldwide.',
                  icon: '🌍',
                  color: '#4facfe',
                  delay: 0.6
                }
              ].map((milestone, index) => (
                <motion.div
                  key={index}
                  initial={{ opacity: 0, y: 100, rotateX: -15 }}
                  whileInView={{ opacity: 1, y: 0, rotateX: 0 }}
                  transition={{ 
                    duration: 0.8, 
                    delay: milestone.delay,
                    type: "spring",
                    stiffness: 100
                  }}
                  viewport={{ once: true }}
                  style={{
                    perspective: '1000px',
                  }}
                >
                  <motion.div
                    whileHover={{ 
                      y: -10, 
                      rotateX: 5,
                      boxShadow: '0 30px 60px rgba(102, 126, 234, 0.3)'
                    }}
                    style={{
                      background: `linear-gradient(135deg, rgba(${milestone.color === '#667eea' ? '102, 126, 234' : milestone.color === '#764ba2' ? '118, 75, 162' : milestone.color === '#f093fb' ? '240, 147, 251' : '79, 172, 254'}, 0.1), rgba(0, 0, 0, 0.8))`,
                      padding: '2.5rem',
                      borderRadius: '20px',
                      border: `2px solid ${milestone.color}20`,
                      position: 'relative',
                      overflow: 'hidden',
                      transformStyle: 'preserve-3d',
                      transition: 'all 0.3s ease',
                    }}
                  >
                    {/* Glow Effect */}
                    <div style={{
                      position: 'absolute',
                      top: '-50%',
                      left: '-50%',
                      width: '200%',
                      height: '200%',
                      background: `radial-gradient(circle, ${milestone.color}40 0%, transparent 70%)`,
                      opacity: 0.5,
                    }} />

                    {/* Year Badge */}
                    <motion.div
                      initial={{ scale: 0 }}
                      whileInView={{ scale: 1 }}
                      transition={{ duration: 0.5, delay: milestone.delay + 0.2 }}
                      viewport={{ once: true }}
                      style={{
                        position: 'absolute',
                        top: '-15px',
                        right: '-15px',
                        background: `linear-gradient(135deg, ${milestone.color}, ${milestone.color}80)`,
                        color: 'white',
                        padding: '0.5rem 1rem',
                        borderRadius: '20px',
                        fontSize: '0.9rem',
                        fontWeight: 'bold',
                        boxShadow: `0 10px 20px ${milestone.color}40`,
                      }}
                    >
                      {milestone.year}
                    </motion.div>

                    {/* Icon */}
                    <motion.div
                      initial={{ scale: 0, rotate: -180 }}
                      whileInView={{ scale: 1, rotate: 0 }}
                      transition={{ duration: 0.8, delay: milestone.delay + 0.3, type: "spring" }}
                      viewport={{ once: true }}
                      style={{
                        fontSize: '3rem',
                        marginBottom: '1.5rem',
                        filter: `drop-shadow(0 10px 20px ${milestone.color}40)`,
                      }}
                    >
                      {milestone.icon}
                    </motion.div>

                    {/* Content */}
                    <div style={{ position: 'relative', zIndex: 2 }}>
                      <h3 style={{
                        fontSize: '1.5rem',
                        color: 'var(--text-primary)',
                        marginBottom: '1rem',
                        background: `linear-gradient(135deg, ${milestone.color}, ${milestone.color}80)`,
                        WebkitBackgroundClip: 'text',
                        WebkitTextFillColor: 'transparent',
                        backgroundClip: 'text',
                      }}>
                        {milestone.title}
                      </h3>
                      <p style={{
                        color: 'var(--text-secondary)',
                        lineHeight: 1.6,
                        fontSize: '0.95rem',
                      }}>
                        {milestone.description}
                      </p>
                    </div>

                    {/* Decorative Elements */}
                    <div style={{
                      position: 'absolute',
                      bottom: '10px',
                      left: '10px',
                      right: '10px',
                      height: '2px',
                      background: `linear-gradient(90deg, transparent, ${milestone.color}, transparent)`,
                      opacity: 0.5,
                    }} />
                  </motion.div>
                </motion.div>
              ))}
            </div>

            {/* Connecting Lines */}
            <svg style={{
              position: 'absolute',
              top: '50%',
              left: '0',
              right: '0',
              height: '2px',
              zIndex: 1,
            }}>
              <defs>
                <linearGradient id="journeyGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                  <stop offset="0%" style={{ stopColor: '#667eea', stopOpacity: 0.3 }} />
                  <stop offset="50%" style={{ stopColor: '#764ba2', stopOpacity: 0.5 }} />
                  <stop offset="100%" style={{ stopColor: '#f093fb', stopOpacity: 0.3 }} />
                </linearGradient>
              </defs>
              <motion.line
                x1="0"
                y1="1"
                x2="100%"
                y2="1"
                stroke="url(#journeyGradient)"
                strokeWidth="2"
                initial={{ pathLength: 0 }}
                whileInView={{ pathLength: 1 }}
                transition={{ duration: 2, delay: 0.5 }}
                viewport={{ once: true }}
              />
            </svg>
          </div>
        </section>

        {/* Call to Action */}
        <section className="cta-section" style={{
          padding: '6rem 0',
          background: 'linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%)',
          textAlign: 'center',
          position: 'relative',
          overflow: 'hidden',
        }}>
          {/* Background Pattern */}
          <div style={{
            position: 'absolute',
            inset: 0,
            opacity: 0.1,
            backgroundImage: `url("data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.4"%3E%3Ccircle cx="30" cy="30" r="4"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")`,
          }} />

          <div className="container" style={{ position: 'relative', zIndex: 2 }}>
            <motion.div
              initial={{ opacity: 0, y: 50 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ duration: 1 }}
              viewport={{ once: true }}
            >
              <h2 style={{
                fontFamily: "'Playfair Display', serif",
                fontSize: '3rem',
                color: 'white',
                marginBottom: '1.5rem',
                textShadow: '0 10px 30px rgba(0,0,0,0.3)',
              }}>
                Let's Create Something Beautiful Together
              </h2>
              <p style={{
                fontSize: '1.2rem',
                color: 'rgba(255,255,255,0.9)',
                maxWidth: '600px',
                margin: '0 auto 3rem',
                lineHeight: 1.6,
              }}>
                Whether it's a collaboration, performance opportunity, or just a hello - I'd love to hear from you.
              </p>
              <motion.button
                whileHover={{ scale: 1.05 }}
                whileTap={{ scale: 0.95 }}
                style={{
                  background: 'white',
                  color: '#667eea',
                  padding: '1rem 3rem',
                  border: 'none',
                  borderRadius: '50px',
                  fontSize: '1.1rem',
                  fontWeight: 'bold',
                  cursor: 'pointer',
                  boxShadow: '0 20px 40px rgba(0,0,0,0.2)',
                  transition: 'all 0.3s ease',
                }}
                onClick={() => window.location.href = '/contact'}
              >
                Get In Touch
              </motion.button>
            </motion.div>
          </div>
        </section>
      </div>
  );
};

export default About;
