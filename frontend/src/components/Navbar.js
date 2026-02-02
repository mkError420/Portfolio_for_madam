import React, { useState, useEffect } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { motion, AnimatePresence } from 'framer-motion';
import ButtonStyles from '../styles/ButtonStyles';

const Navbar = () => {
  const [isOpen, setIsOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);
  const [hoveredItem, setHoveredItem] = useState(null);
  const location = useLocation();

  useEffect(() => {
    const handleScroll = () => {
      setScrolled(window.scrollY > 30);
    };

    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const navItems = [
    { path: '/', label: 'Home', icon: '🏠' },
    { path: '/about', label: 'About', icon: '👤' },
    { path: '/music', label: 'Music', icon: '🎵' },
    { path: '/videos', label: 'Videos', icon: '🎬' },
    { path: '/gallery', label: 'Gallery', icon: '🖼️' },
    { path: '/tour', label: 'Tour', icon: '🎪' },
    { path: '/contact', label: 'Contact', icon: '📧' },
  ];

  return (
    <>
      <style jsx>{`
        ${ButtonStyles}
        
        /* Enhanced Responsive Design */
        
        /* Tablet: 1024px and below */
        @media (max-width: 1024px) {
          .navbar-container {
            padding: 0 1.5rem;
          }
          
          .logo-text {
            font-size: 1.6rem !important;
          }
          
          .logo-icon {
            width: 40px !important;
            height: 40px !important;
            font-size: 1.3rem !important;
          }
          
          .desktop-nav {
            gap: 0.3rem !important;
            padding: 0.4rem 0.8rem !important;
          }
          
          .nav-item {
            padding: 0.6rem 1rem !important;
            font-size: 0.9rem !important;
          }
        }
        
        /* Mobile: 768px and below */
        @media (max-width: 768px) {
          .navbar-container {
            padding: 0 1rem;
          }
          
          .logo-text {
            font-size: 1.4rem !important;
          }
          
          .logo-icon {
            width: 35px !important;
            height: 35px !important;
            font-size: 1.2rem !important;
          }
          
          .mobile-menu-btn {
            width: 45px !important;
            height: 45px !important;
          }
          
          .hamburger {
            width: 20px !important;
            height: 16px !important;
          }
        }
        
        /* Desktop Navigation */
        @media (min-width: 769px) {
          .desktop-nav {
            display: flex !important;
          }
          .mobile-menu-btn {
            display: none !important;
          }
        }
        
        /* Base Styles */
        .navbar-unique {
          position: fixed;
          top: 0;
          left: 0;
          right: 0;
          z-index: 1000;
          background: ${scrolled 
            ? 'linear-gradient(135deg, rgba(0, 0, 0, 0.95) 0%, rgba(15, 15, 15, 0.95) 50%, rgba(0, 0, 0, 0.95) 100%)' 
            : 'linear-gradient(135deg, rgba(0, 0, 0, 0.8) 0%, rgba(15, 15, 15, 0.8) 50%, rgba(0, 0, 0, 0.8) 100%)'
          };
          backdrop-filter: blur(20px);
          transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
          padding: 1rem 0;
          border-bottom: ${scrolled 
            ? '1px solid rgba(102, 126, 234, 0.3)' 
            : '1px solid rgba(255, 255, 255, 0.1)'
          };
          box-shadow: ${scrolled 
            ? '0 10px 40px rgba(0, 0, 0, 0.5), 0 0 20px rgba(102, 126, 234, 0.1)' 
            : '0 5px 20px rgba(0, 0, 0, 0.3)'
          };
        }

        .navbar-container {
          max-width: 1200px;
          margin: 0 auto;
          padding: 0 2rem;
        }

        .navbar-content {
          display: flex;
          justify-content: space-between;
          align-items: center;
        }

        .logo-container {
          position: relative;
          display: flex;
          align-items: center;
          gap: 1rem;
        }

        .logo-icon {
          width: 45px;
          height: 45px;
          background: linear-gradient(135deg, #667eea, #764ba2);
          border-radius: 12px;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 1.5rem;
          color: white;
          box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
          transition: all 0.3s ease;
        }

        .logo-icon:hover {
          transform: translateY(-2px) scale(1.05);
          box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
        }

        .logo-text {
          font-family: 'Playfair Display', serif;
          font-size: 1.8rem;
          font-weight: 700;
          color: #ffffff;
          text-decoration: none;
          background: linear-gradient(135deg, #ffffff, #e0e0e0);
          -webkit-background-clip: text;
          -webkit-text-fill-color: transparent;
          background-clip: text;
          text-shadow: 0 2px 10px rgba(255, 255, 255, 0.3);
          transition: all 0.3s ease;
        }

        .logo-text:hover {
          background: linear-gradient(135deg, #667eea, #764ba2);
          -webkit-background-clip: text;
          -webkit-text-fill-color: transparent;
          background-clip: text;
        }

        .desktop-nav {
          display: none;
          gap: 0.5rem;
          align-items: center;
          background: rgba(255, 255, 255, 0.05);
          padding: 0.5rem 1rem;
          border-radius: 50px;
          backdrop-filter: blur(10px);
          border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .nav-item {
          position: relative;
          padding: 0.8rem 1.2rem;
          color: rgba(255, 255, 255, 0.8);
          text-decoration: none;
          font-weight: 500;
          font-size: 0.95rem;
          border-radius: 25px;
          transition: all 0.3s ease;
          display: flex;
          align-items: center;
          gap: 0.5rem;
        }

        .nav-item:hover {
          color: #ffffff;
          background: rgba(102, 126, 234, 0.2);
          transform: translateY(-1px);
        }

        .nav-item.active {
          color: #ffffff;
          background: linear-gradient(135deg, rgba(102, 126, 234, 0.3), rgba(118, 75, 162, 0.3));
          box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }

        .nav-item-icon {
          font-size: 0.9rem;
          opacity: 0.8;
          transition: all 0.3s ease;
        }

        .nav-item:hover .nav-item-icon,
        .nav-item.active .nav-item-icon {
          opacity: 1;
          transform: scale(1.1);
        }

        .mobile-menu-btn {
          display: block;
          background: rgba(255, 255, 255, 0.1);
          border: 1px solid rgba(255, 255, 255, 0.2);
          color: #ffffff;
          width: 50px;
          height: 50px;
          border-radius: 15px;
          cursor: pointer;
          padding: 0;
          position: relative;
          transition: all 0.3s ease;
          backdrop-filter: blur(10px);
        }

        .mobile-menu-btn:hover {
          background: rgba(102, 126, 234, 0.2);
          border-color: rgba(102, 126, 234, 0.4);
          transform: scale(1.05);
        }

        .hamburger {
          width: 25px;
          height: 20px;
          position: relative;
          transform: ${isOpen ? 'rotate(45deg)' : 'rotate(0)'};
          transition: transform 0.3s ease;
        }

        .hamburger span {
          position: absolute;
          height: 2px;
          width: 100%;
          background: #ffffff;
          border-radius: 2px;
          transition: all 0.3s ease;
        }

        .hamburger span:nth-child(1) {
          top: ${isOpen ? '50%' : '0'};
          transform: ${isOpen ? 'translateY(-50%)' : 'translateY(0)'};
          opacity: ${isOpen ? 0 : 1};
        }

        .hamburger span:nth-child(2) {
          top: 50%;
          transform: translateY(-50%);
        }

        .hamburger span:nth-child(3) {
          bottom: ${isOpen ? '50%' : '0'};
          transform: ${isOpen ? 'translateY(-50%)' : 'translateY(0)'};
        }

        .mobile-nav {
          position: absolute;
          top: 100%;
          left: 0;
          right: 0;
          background: linear-gradient(135deg, rgba(0, 0, 0, 0.98) 0%, rgba(15, 15, 15, 0.98) 100%);
          backdrop-filter: blur(20px);
          border-top: 1px solid rgba(102, 126, 234, 0.3);
          padding: 1rem 0;
          margin-top: 0.5rem;
        }

        .mobile-nav-item {
          display: flex;
          align-items: center;
          gap: 1rem;
          padding: 1rem 2rem;
          color: rgba(255, 255, 255, 0.8);
          text-decoration: none;
          font-weight: 500;
          font-size: 1rem;
          transition: all 0.3s ease;
          border-left: 3px solid transparent;
        }

        .mobile-nav-item:hover {
          color: #ffffff;
          background: rgba(102, 126, 234, 0.1);
          border-left-color: #667eea;
          padding-left: 2.5rem;
        }

        .mobile-nav-item.active {
          color: #ffffff;
          background: rgba(102, 126, 234, 0.2);
          border-left-color: #667eea;
        }

        .mobile-nav-icon {
          font-size: 1.2rem;
          opacity: 0.8;
        }
      `}</style>

      <motion.nav
        initial={{ y: -100 }}
        animate={{ y: 0 }}
        transition={{ duration: 0.6 }}
        className="navbar-unique"
      >
        <div className="navbar-container">
          <div className="navbar-content">
            {/* Logo */}
            <Link to="/" className="logo-container">
              <motion.div 
                className="logo-icon"
                whileHover={{ scale: 1.1, rotate: 5 }}
                whileTap={{ scale: 0.95 }}
              >
                🎵
              </motion.div>
              <div className="logo-text">
                Artist Name
              </div>
            </Link>

            {/* Desktop Navigation */}
            <div className="desktop-nav">
              {navItems.map((item) => (
                <Link
                  key={item.path}
                  to={item.path}
                  className={`nav-item ${location.pathname === item.path ? 'active' : ''}`}
                  onMouseEnter={() => setHoveredItem(item.path)}
                  onMouseLeave={() => setHoveredItem(null)}
                >
                  <span className="nav-item-icon">{item.icon}</span>
                  <span>{item.label}</span>
                  {location.pathname === item.path && (
                    <motion.div
                      layoutId="navbar-indicator"
                      style={{
                        position: 'absolute',
                        bottom: '-2px',
                        left: '50%',
                        transform: 'translateX(-50%)',
                        width: '20px',
                        height: '2px',
                        background: 'linear-gradient(90deg, #667eea, #764ba2)',
                        borderRadius: '2px',
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
            >
              <div className="hamburger">
                <span></span>
                <span></span>
                <span></span>
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
                className="mobile-nav"
              >
                {navItems.map((item) => (
                  <Link
                    key={item.path}
                    to={item.path}
                    onClick={() => setIsOpen(false)}
                    className={`mobile-nav-item ${location.pathname === item.path ? 'active' : ''}`}
                  >
                    <span className="mobile-nav-icon">{item.icon}</span>
                    <span>{item.label}</span>
                  </Link>
                ))}
              </motion.div>
            )}
          </AnimatePresence>
        </div>
      </motion.nav>
    </>
  );
};

export default Navbar;
