import React from 'react';
import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import ButtonStyles from '../styles/ButtonStyles';

const Footer = () => {
  const socialLinks = [
    { name: 'Facebook', url: '#', icon: 'f' },
    { name: 'Twitter', url: '#', icon: '𝕏' },
    { name: 'Instagram', url: '#', icon: '📷' },
    { name: 'YouTube', url: '#', icon: '▶' },
    { name: 'Spotify', url: '#', icon: '♫' },
  ];

  return (
    <>
      <style>
        {`
          ${ButtonStyles}
          
          .footer-container {
            background: var(--secondary-color);
            padding: 3rem 0 2rem;
            margin-top: 4rem;
            border-top: 1px solid var(--border-color);
          }

          .footer-grid {
            display: grid;
            gap: 2rem;
            margin-bottom: 2rem;
          }

          /* Mobile: 1 column */
          @media (max-width: 575px) {
            .footer-grid {
              grid-template-columns: 1fr;
            }
            
            .artist-info,
            .newsletter-section {
              grid-column: 1;
            }
            
            .subscribe-form {
              flex-direction: column;
            }
            
            .email-input {
              border-radius: 50px !important;
              margin-bottom: 0.5rem;
            }
            
            .subscribe-btn {
              border-radius: 50px !important;
              width: 100%;
            }
            
            .social-grid {
              grid-template-columns: repeat(3, 1fr);
            }
          }

          /* Small: 2 columns */
          @media (min-width: 576px) and (max-width: 767px) {
            .footer-grid {
              grid-template-columns: 1fr 1fr;
            }
            
            .artist-info,
            .newsletter-section {
              grid-column: span 2;
            }
            
            .subscribe-form {
              flex-direction: row;
            }
            
            .email-input {
              border-radius: 50px 0 0 50px !important;
              margin-bottom: 0;
            }
            
            .subscribe-btn {
              border-radius: 0 50px 50px 0 !important;
              width: auto;
            }
            
            .social-grid {
              grid-template-columns: repeat(3, 1fr);
            }
          }

          /* Medium: 3 columns */
          @media (min-width: 768px) and (max-width: 991px) {
            .footer-grid {
              grid-template-columns: 2fr 1fr 1fr;
            }
            
            .artist-info {
              grid-column: span 1;
            }
            
            .newsletter-section {
              grid-column: span 1;
            }
            
            .subscribe-form {
              flex-direction: column;
            }
            
            .email-input {
              border-radius: 50px !important;
              margin-bottom: 0.5rem;
            }
            
            .subscribe-btn {
              border-radius: 50px !important;
              width: 100%;
            }
            
            .social-grid {
              grid-template-columns: repeat(3, 1fr);
            }
          }

          /* Large: 4 columns */
          @media (min-width: 992px) {
            .footer-grid {
              grid-template-columns: 2fr 1fr 1fr 1fr;
            }
            
            .artist-info {
              grid-column: span 1;
            }
            
            .newsletter-section {
              grid-column: span 1;
            }
            
            .subscribe-form {
              flex-direction: row;
            }
            
            .email-input {
              border-radius: 50px 0 0 50px !important;
              margin-bottom: 0;
              min-width: 200px;
            }
            
            .subscribe-btn {
              border-radius: 0 50px 50px 0 !important;
              width: auto;
            }
            
            .social-grid {
              grid-template-columns: repeat(3, 1fr);
            }
          }

          .artist-info h3 {
            font-family: 'Playfair Display', serif;
            color: var(--text-primary);
            margin-bottom: 1rem;
          }

          .artist-info p {
            color: var(--text-secondary);
            line-height: 1.6;
          }

          .section-title {
            color: var(--text-primary);
            margin-bottom: 1rem;
            font-size: 1.1rem;
          }

          .quick-links {
            list-style: none;
            padding: 0;
          }

          .quick-links li {
            margin-bottom: 0.5rem;
          }

          .quick-links a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.3s ease;
          }

          .quick-links a:hover {
            color: var(--accent-color);
          }

          .social-grid {
            display: grid;
            gap: 0.5rem;
          }

          .social-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 50%;
            color: var(--text-primary);
            text-decoration: none;
            transition: all 0.3s ease;
          }

          .social-link:hover {
            background: var(--accent-color);
            border-color: var(--accent-color);
          }

          .social-link span {
            font-size: 1.2rem;
          }

          .newsletter-section h4 {
            color: var(--text-primary);
            margin-bottom: 1rem;
            font-size: 1.1rem;
          }

          .newsletter-section p {
            color: var(--text-secondary);
            margin-bottom: 1rem;
            font-size: 0.9rem;
          }

          .subscribe-form {
            display: flex;
            gap: 0.5rem;
          }

          .email-input {
            flex: 1;
            padding: 0.8rem 1rem;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.3s ease;
          }

          .email-input:focus {
            border-color: var(--accent-color);
          }

          .subscribe-btn {
            padding: 0.8rem 1.5rem;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
          }

          .copyright {
            border-top: 1px solid var(--border-color);
            padding-top: 2rem;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.9rem;
          }

          .copyright p {
            margin: 0;
          }
        `}
      </style>

      <motion.footer
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        transition={{ duration: 0.6 }}
        className="footer-container"
      >
        <div className="container">
          {/* Main Footer Content */}
          <div className="footer-grid">
            {/* Artist Info */}
            <div className="artist-info">
              <h3>Artist Name</h3>
              <p>
                Professional singer and performer, bringing soulful music to audiences worldwide.
              </p>
            </div>

            {/* Quick Links */}
            <div>
              <h4 className="section-title">Quick Links</h4>
              <ul className="quick-links">
                {['Home', 'About', 'Music', 'Videos', 'Gallery', 'Tour', 'Contact'].map((item) => (
                  <li key={item}>
                    <Link
                      to={`/${item.toLowerCase() === 'home' ? '' : item.toLowerCase()}`}
                    >
                      {item}
                    </Link>
                  </li>
                ))}
              </ul>
            </div>

            {/* Social Media */}
            <div>
              <h4 className="section-title">Follow Me</h4>
              <div className="social-grid">
                {socialLinks.map((social) => (
                  <motion.a
                    key={social.name}
                    href={social.url}
                    target="_blank"
                    rel="noopener noreferrer"
                    whileHover={{ scale: 1.1 }}
                    whileTap={{ scale: 0.95 }}
                    className="social-link"
                  >
                    <span>{social.icon}</span>
                  </motion.a>
                ))}
              </div>
            </div>

            {/* Newsletter */}
            <div className="newsletter-section">
              <h4>Stay Updated</h4>
              <p>
                Subscribe to get the latest updates on new music and events.
              </p>
              <form onSubmit={(e) => e.preventDefault()} className="subscribe-form">
                <input
                  type="email"
                  placeholder="Your email"
                  className="email-input"
                  required
                />
                <button type="submit" className="btn btn-primary btn-gradient-text">
                  Subscribe
                </button>
              </form>
            </div>
          </div>

          {/* Copyright */}
          <div className="copyright">
            <p>&copy; {new Date().getFullYear()} Artist Name. All rights reserved.</p>
          </div>
        </div>
      </motion.footer>
    </>
  );
};

export default Footer;
