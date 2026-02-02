import React, { useState, useEffect } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { motion, AnimatePresence } from 'framer-motion';

const Navbar = () => {
  const [isOpen, setIsOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);
  const location = useLocation();

  useEffect(() => {
    const handleScroll = () => {
      setScrolled(window.scrollY > 50);
    };

    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const navItems = [
    { path: '/', label: 'Home' },
    { path: '/about', label: 'About' },
    { path: '/music', label: 'Music' },
    { path: '/videos', label: 'Videos' },
    { path: '/gallery', label: 'Gallery' },
    { path: '/tour', label: 'Tour' },
    { path: '/contact', label: 'Contact' },
  ];

  return (
    <motion.nav
      className={`navbar ${scrolled ? 'scrolled' : ''}`}
      initial={{ y: -100 }}
      animate={{ y: 0 }}
      transition={{ duration: 0.6 }}
      style={{
        position: 'fixed',
        top: 0,
        width: '100%',
        zIndex: 1000,
        background: scrolled ? 'rgba(26, 26, 26, 0.95)' : 'transparent',
        backdropFilter: scrolled ? 'blur(10px)' : 'none',
        transition: 'all 0.3s ease',
        padding: '1rem 0',
        borderBottom: scrolled ? '1px solid rgba(255, 255, 255, 0.1)' : 'none',
      }}
    >
      <div className="container">
        <div style={{
          display: 'flex',
          justifyContent: 'space-between',
          alignItems: 'center',
        }}>
          <Link to="/" style={{
            fontFamily: "'Playfair Display', serif",
            fontSize: '1.8rem',
            fontWeight: 700,
            color: 'var(--text-primary)',
            textDecoration: 'none',
          }}>
            Artist Name
          </Link>

          {/* Desktop Navigation */}
          <div className="desktop-nav" style={{
            display: 'none',
            gap: '2rem',
            alignItems: 'center',
          }}>
            {navItems.map((item) => (
              <Link
                key={item.path}
                to={item.path}
                style={{
                  color: location.pathname === item.path ? 'var(--accent-color)' : 'var(--text-primary)',
                  textDecoration: 'none',
                  fontWeight: 500,
                  transition: 'color 0.3s ease',
                  position: 'relative',
                }}
                onMouseEnter={(e) => {
                  e.target.style.color = 'var(--accent-color)';
                }}
                onMouseLeave={(e) => {
                  e.target.style.color = location.pathname === item.path ? 'var(--accent-color)' : 'var(--text-primary)';
                }}
              >
                {item.label}
                {location.pathname === item.path && (
                  <motion.div
                    layoutId="navbar-indicator"
                    style={{
                      position: 'absolute',
                      bottom: '-5px',
                      left: 0,
                      right: 0,
                      height: '2px',
                      background: 'var(--accent-color)',
                    }}
                  />
                )}
              </Link>
            ))}
          </div>

          {/* Mobile Menu Button */}
          <button
            className="mobile-menu-btn"
            onClick={() => setIsOpen(!isOpen)}
            style={{
              display: 'block',
              background: 'none',
              border: 'none',
              color: 'var(--text-primary)',
              fontSize: '1.5rem',
              cursor: 'pointer',
              padding: '0.5rem',
            }}
          >
            <div style={{
              width: '25px',
              height: '20px',
              position: 'relative',
              transform: isOpen ? 'rotate(45deg)' : 'rotate(0)',
              transition: 'transform 0.3s ease',
            }}>
              <span style={{
                position: 'absolute',
                height: '2px',
                width: '100%',
                background: 'var(--text-primary)',
                top: isOpen ? '50%' : '0',
                transform: isOpen ? 'translateY(-50%)' : 'translateY(0)',
                transition: 'all 0.3s ease',
              }} />
              <span style={{
                position: 'absolute',
                height: '2px',
                width: '100%',
                background: 'var(--text-primary)',
                top: '50%',
                transform: 'translateY(-50%)',
                opacity: isOpen ? 0 : 1,
                transition: 'opacity 0.3s ease',
              }} />
              <span style={{
                position: 'absolute',
                height: '2px',
                width: '100%',
                background: 'var(--text-primary)',
                bottom: isOpen ? '50%' : '0',
                transform: isOpen ? 'translateY(-50%)' : 'translateY(0)',
                transition: 'all 0.3s ease',
              }} />
            </div>
          </button>
        </div>

        {/* Mobile Navigation */}
        <AnimatePresence>
          {isOpen && (
            <motion.div
              initial={{ opacity: 0, height: 0 }}
              animate={{ opacity: 1, height: 'auto' }}
              exit={{ opacity: 0, height: 0 }}
              transition={{ duration: 0.3 }}
              style={{
                marginTop: '1rem',
                paddingTop: '1rem',
                borderTop: '1px solid var(--border-color)',
              }}
            >
              {navItems.map((item) => (
                <Link
                  key={item.path}
                  to={item.path}
                  onClick={() => setIsOpen(false)}
                  style={{
                    display: 'block',
                    padding: '0.8rem 0',
                    color: location.pathname === item.path ? 'var(--accent-color)' : 'var(--text-primary)',
                    textDecoration: 'none',
                    fontWeight: 500,
                    transition: 'color 0.3s ease',
                  }}
                >
                  {item.label}
                </Link>
              ))}
            </motion.div>
          )}
        </AnimatePresence>
      </div>

      <style jsx>{`
        @media (min-width: 768px) {
          .desktop-nav {
            display: flex !important;
          }
          .mobile-menu-btn {
            display: none !important;
          }
        }
      `}</style>
    </motion.nav>
  );
};

export default Navbar;
