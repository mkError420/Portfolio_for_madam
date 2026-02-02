import React from 'react';
import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';

const Footer = () => {
  const socialLinks = [
    { name: 'Facebook', url: '#', icon: 'f' },
    { name: 'Twitter', url: '#', icon: '𝕏' },
    { name: 'Instagram', url: '#', icon: '📷' },
    { name: 'YouTube', url: '#', icon: '▶' },
    { name: 'Spotify', url: '#', icon: '♫' },
  ];

  return (
    <motion.footer
      initial={{ opacity: 0 }}
      animate={{ opacity: 1 }}
      transition={{ duration: 0.6 }}
      style={{
        background: 'var(--secondary-color)',
        padding: '3rem 0 2rem',
        marginTop: '4rem',
        borderTop: '1px solid var(--border-color)',
      }}
    >
      <div className="container">
        {/* Main Footer Content */}
        <div style={{
          display: 'grid',
          gridTemplateColumns: {
            xs: '1fr',
            sm: '1fr 1fr',
            md: '2fr 1fr 1fr',
            lg: '2fr 1fr 1fr 1fr'
          },
          gap: '2rem',
          marginBottom: '2rem',
        }}>
          {/* Artist Info */}
          <div style={{
            gridColumn: {
              xs: '1',
              sm: 'span 2',
              md: 'span 1',
              lg: 'span 1'
            }
          }}>
            <h3 style={{
              fontFamily: "'Playfair Display', serif",
              color: 'var(--text-primary)',
              marginBottom: '1rem',
            }}>
              Artist Name
            </h3>
            <p style={{
              color: 'var(--text-secondary)',
              lineHeight: 1.6,
            }}>
              Professional singer and performer, bringing soulful music to audiences worldwide.
            </p>
          </div>

          {/* Quick Links */}
          <div>
            <h4 style={{
              color: 'var(--text-primary)',
              marginBottom: '1rem',
              fontSize: '1.1rem',
            }}>
              Quick Links
            </h4>
            <ul style={{
              listStyle: 'none',
              padding: 0,
            }}>
              {['Home', 'About', 'Music', 'Videos', 'Gallery', 'Tour', 'Contact'].map((item) => (
                <li key={item} style={{ marginBottom: '0.5rem' }}>
                  <Link
                    to={`/${item.toLowerCase() === 'home' ? '' : item.toLowerCase()}`}
                    style={{
                      color: 'var(--text-secondary)',
                      textDecoration: 'none',
                      transition: 'color 0.3s ease',
                    }}
                    onMouseEnter={(e) => {
                      e.target.style.color = 'var(--accent-color)';
                    }}
                    onMouseLeave={(e) => {
                      e.target.style.color = 'var(--text-secondary)';
                    }}
                  >
                    {item}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Social Media */}
          <div>
            <h4 style={{
              color: 'var(--text-primary)',
              marginBottom: '1rem',
              fontSize: '1.1rem',
            }}>
              Follow Me
            </h4>
            <div style={{
              display: 'grid',
              gridTemplateColumns: 'repeat(3, 1fr)',
              gap: '0.5rem',
            }}>
              {socialLinks.map((social) => (
                <motion.a
                  key={social.name}
                  href={social.url}
                  target="_blank"
                  rel="noopener noreferrer"
                  whileHover={{ scale: 1.1 }}
                  whileTap={{ scale: 0.95 }}
                  style={{
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    width: '40px',
                    height: '40px',
                    background: 'var(--card-bg)',
                    border: '1px solid var(--border-color)',
                    borderRadius: '50%',
                    color: 'var(--text-primary)',
                    textDecoration: 'none',
                    transition: 'all 0.3s ease',
                  }}
                  onMouseEnter={(e) => {
                    e.target.style.background = 'var(--accent-color)';
                    e.target.style.borderColor = 'var(--accent-color)';
                  }}
                  onMouseLeave={(e) => {
                    e.target.style.background = 'var(--card-bg)';
                    e.target.style.borderColor = 'var(--border-color)';
                  }}
                >
                  <span style={{ fontSize: '1.2rem' }}>{social.icon}</span>
                </motion.a>
              ))}
            </div>
          </div>

          {/* Newsletter - Better Responsive Design */}
          <div style={{
            gridColumn: {
              xs: '1',
              sm: 'span 2',
              md: 'span 1',
              lg: 'span 1'
            }
          }}>
            <h4 style={{
              color: 'var(--text-primary)',
              marginBottom: '1rem',
              fontSize: '1.1rem',
            }}>
              Stay Updated
            </h4>
            <p style={{
              color: 'var(--text-secondary)',
              marginBottom: '1rem',
              fontSize: '0.9rem',
            }}>
              Subscribe to get the latest updates on new music and events.
            </p>
            <form onSubmit={(e) => e.preventDefault()}>
              <div style={{
                display: 'flex',
                flexDirection: {
                  xs: 'column',
                  sm: 'row'
                },
                gap: '0.5rem',
              }}>
                <input
                  type="email"
                  placeholder="Your email"
                  style={{
                    flex: 1,
                    padding: '0.8rem 1rem',
                    background: 'var(--card-bg)',
                    border: '1px solid var(--border-color)',
                    borderRadius: {
                      xs: '50px',
                      sm: '50px 0 0 50px'
                    },
                    color: 'var(--text-primary)',
                    fontSize: '0.9rem',
                    minWidth: {
                      sm: '200px'
                    }
                  }}
                />
                <button
                  type="submit"
                  className="btn btn-primary"
                  style={{
                    padding: '0.8rem 1.5rem',
                    fontSize: '0.9rem',
                    borderRadius: {
                      xs: '50px',
                      sm: '0 50px 50px 0'
                    },
                    minWidth: {
                      xs: '100%',
                      sm: 'auto'
                    }
                  }}
                >
                  Subscribe
                </button>
              </div>
            </form>
          </div>
        </div>

        {/* Copyright */}
        <div style={{
          borderTop: '1px solid var(--border-color)',
          paddingTop: '2rem',
          textAlign: 'center',
          color: 'var(--text-muted)',
          fontSize: '0.9rem',
        }}>
          <p>&copy; {new Date().getFullYear()} Artist Name. All rights reserved.</p>
        </div>
      </div>
    </motion.footer>
  );
};

export default Footer;
