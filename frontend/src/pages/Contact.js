import React, { useState } from 'react';
import { motion } from 'framer-motion';

const Contact = () => {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    subject: '',
    message: '',
    type: 'general'
  });
  const [errors, setErrors] = useState({});
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [submitStatus, setSubmitStatus] = useState('');

  const contactInfo = [
    {
      icon: '📧',
      title: 'Email',
      content: 'contact@artistname.com',
      link: 'mailto:contact@artistname.com'
    },
    {
      icon: '📱',
      title: 'Phone',
      content: '+1 (555) 123-4567',
      link: 'tel:+15551234567'
    },
    {
      icon: '📍',
      title: 'Management',
      content: 'Artist Management Inc.',
      link: null
    },
    {
      icon: '🎵',
      title: 'Booking',
      content: 'booking@artistname.com',
      link: 'mailto:booking@artistname.com'
    }
  ];

  const socialLinks = [
    { name: 'Facebook', url: '#', icon: 'f' },
    { name: 'Twitter', url: '#', icon: '𝕏' },
    { name: 'Instagram', url: '#', icon: '📷' },
    { name: 'YouTube', url: '#', icon: '▶' },
    { name: 'Spotify', url: '#', icon: '♫' },
    { name: 'TikTok', url: '#', icon: '🎵' }
  ];

  const validateForm = () => {
    const newErrors = {};

    if (!formData.name.trim()) {
      newErrors.name = 'Name is required';
    }

    if (!formData.email.trim()) {
      newErrors.email = 'Email is required';
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
      newErrors.email = 'Please enter a valid email address';
    }

    if (!formData.subject.trim()) {
      newErrors.subject = 'Subject is required';
    }

    if (!formData.message.trim()) {
      newErrors.message = 'Message is required';
    } else if (formData.message.trim().length < 10) {
      newErrors.message = 'Message must be at least 10 characters long';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));

    // Clear error for this field when user starts typing
    if (errors[name]) {
      setErrors(prev => ({
        ...prev,
        [name]: ''
      }));
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    
    if (!validateForm()) {
      return;
    }

    setIsSubmitting(true);
    setSubmitStatus('');

    try {
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 2000));
      
      setSubmitStatus('success');
      setFormData({
        name: '',
        email: '',
        subject: '',
        message: '',
        type: 'general'
      });
    } catch (error) {
      setSubmitStatus('error');
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div className="contact">
      {/* Hero Section */}
      <section className="contact-hero" style={{
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
              Get in Touch
            </h1>
            <p style={{
              fontSize: '1.2rem',
              color: 'var(--text-secondary)',
              maxWidth: '600px',
              margin: '0 auto',
              lineHeight: 1.6,
            }}>
              I'd love to hear from you. Whether it's about music, collaborations, or just saying hello!
            </p>
          </motion.div>
        </div>
      </section>

      {/* Contact Content */}
      <section className="contact-content" style={{
        padding: '5rem 0',
        background: 'var(--primary-color)',
      }}>
        <div className="container">
          <div style={{
            display: 'grid',
            gridTemplateColumns: 'repeat(auto-fit, minmax(350px, 1fr))',
            gap: '3rem',
          }}>
            {/* Contact Form */}
            <motion.div
              initial={{ opacity: 0, x: -50 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.8 }}
            >
              <div className="card">
                <h2 style={{
                  color: 'var(--text-primary)',
                  marginBottom: '2rem',
                  fontSize: '1.8rem',
                }}>
                  Send a Message
                </h2>

                {submitStatus === 'success' && (
                  <motion.div
                    initial={{ opacity: 0, y: -20 }}
                    animate={{ opacity: 1, y: 0 }}
                    style={{
                      background: 'rgba(76, 175, 80, 0.1)',
                      border: '1px solid #4CAF50',
                      color: '#4CAF50',
                      padding: '1rem',
                      borderRadius: '8px',
                      marginBottom: '1.5rem',
                    }}
                  >
                    Thank you for your message! I'll get back to you soon.
                  </motion.div>
                )}

                {submitStatus === 'error' && (
                  <motion.div
                    initial={{ opacity: 0, y: -20 }}
                    animate={{ opacity: 1, y: 0 }}
                    style={{
                      background: 'rgba(244, 67, 54, 0.1)',
                      border: '1px solid #f44336',
                      color: '#f44336',
                      padding: '1rem',
                      borderRadius: '8px',
                      marginBottom: '1.5rem',
                    }}
                  >
                    Something went wrong. Please try again later.
                  </motion.div>
                )}

                <form onSubmit={handleSubmit}>
                  <div style={{ marginBottom: '1.5rem' }}>
                    <label style={{
                      display: 'block',
                      color: 'var(--text-primary)',
                      marginBottom: '0.5rem',
                      fontWeight: 500,
                    }}>
                      Name *
                    </label>
                    <input
                      type="text"
                      name="name"
                      value={formData.name}
                      onChange={handleInputChange}
                      style={{
                        width: '100%',
                        padding: '0.8rem',
                        background: 'var(--card-bg)',
                        border: errors.name ? '1px solid #f44336' : '1px solid var(--border-color)',
                        borderRadius: '8px',
                        color: 'var(--text-primary)',
                        fontSize: '1rem',
                      }}
                      placeholder="Your name"
                    />
                    {errors.name && (
                      <span style={{ color: '#f44336', fontSize: '0.85rem' }}>
                        {errors.name}
                      </span>
                    )}
                  </div>

                  <div style={{ marginBottom: '1.5rem' }}>
                    <label style={{
                      display: 'block',
                      color: 'var(--text-primary)',
                      marginBottom: '0.5rem',
                      fontWeight: 500,
                    }}>
                      Email *
                    </label>
                    <input
                      type="email"
                      name="email"
                      value={formData.email}
                      onChange={handleInputChange}
                      style={{
                        width: '100%',
                        padding: '0.8rem',
                        background: 'var(--card-bg)',
                        border: errors.email ? '1px solid #f44336' : '1px solid var(--border-color)',
                        borderRadius: '8px',
                        color: 'var(--text-primary)',
                        fontSize: '1rem',
                      }}
                      placeholder="your.email@example.com"
                    />
                    {errors.email && (
                      <span style={{ color: '#f44336', fontSize: '0.85rem' }}>
                        {errors.email}
                      </span>
                    )}
                  </div>

                  <div style={{ marginBottom: '1.5rem' }}>
                    <label style={{
                      display: 'block',
                      color: 'var(--text-primary)',
                      marginBottom: '0.5rem',
                      fontWeight: 500,
                    }}>
                      Subject *
                    </label>
                    <input
                      type="text"
                      name="subject"
                      value={formData.subject}
                      onChange={handleInputChange}
                      style={{
                        width: '100%',
                        padding: '0.8rem',
                        background: 'var(--card-bg)',
                        border: errors.subject ? '1px solid #f44336' : '1px solid var(--border-color)',
                        borderRadius: '8px',
                        color: 'var(--text-primary)',
                        fontSize: '1rem',
                      }}
                      placeholder="What's this about?"
                    />
                    {errors.subject && (
                      <span style={{ color: '#f44336', fontSize: '0.85rem' }}>
                        {errors.subject}
                      </span>
                    )}
                  </div>

                  <div style={{ marginBottom: '1.5rem' }}>
                    <label style={{
                      display: 'block',
                      color: 'var(--text-primary)',
                      marginBottom: '0.5rem',
                      fontWeight: 500,
                    }}>
                      Message Type
                    </label>
                    <select
                      name="type"
                      value={formData.type}
                      onChange={handleInputChange}
                      style={{
                        width: '100%',
                        padding: '0.8rem',
                        background: 'var(--card-bg)',
                        border: '1px solid var(--border-color)',
                        borderRadius: '8px',
                        color: 'var(--text-primary)',
                        fontSize: '1rem',
                      }}
                    >
                      <option value="general">General Inquiry</option>
                      <option value="booking">Booking</option>
                      <option value="collaboration">Collaboration</option>
                      <option value="press">Press & Media</option>
                      <option value="fan">Fan Message</option>
                    </select>
                  </div>

                  <div style={{ marginBottom: '1.5rem' }}>
                    <label style={{
                      display: 'block',
                      color: 'var(--text-primary)',
                      marginBottom: '0.5rem',
                      fontWeight: 500,
                    }}>
                      Message *
                    </label>
                    <textarea
                      name="message"
                      value={formData.message}
                      onChange={handleInputChange}
                      rows="5"
                      style={{
                        width: '100%',
                        padding: '0.8rem',
                        background: 'var(--card-bg)',
                        border: errors.message ? '1px solid #f44336' : '1px solid var(--border-color)',
                        borderRadius: '8px',
                        color: 'var(--text-primary)',
                        fontSize: '1rem',
                        resize: 'vertical',
                        fontFamily: 'inherit',
                      }}
                      placeholder="Your message here..."
                    />
                    {errors.message && (
                      <span style={{ color: '#f44336', fontSize: '0.85rem' }}>
                        {errors.message}
                      </span>
                    )}
                  </div>

                  <button
                    type="submit"
                    disabled={isSubmitting}
                    className="btn btn-primary"
                    style={{
                      width: '100%',
                      padding: '1rem',
                      fontSize: '1.1rem',
                      opacity: isSubmitting ? 0.7 : 1,
                      cursor: isSubmitting ? 'not-allowed' : 'pointer',
                    }}
                  >
                    {isSubmitting ? 'Sending...' : 'Send Message'}
                  </button>
                </form>
              </div>
            </motion.div>

            {/* Contact Information */}
            <motion.div
              initial={{ opacity: 0, x: 50 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ duration: 0.8, delay: 0.2 }}
            >
              <div className="card" style={{ marginBottom: '2rem' }}>
                <h2 style={{
                  color: 'var(--text-primary)',
                  marginBottom: '2rem',
                  fontSize: '1.8rem',
                }}>
                  Contact Information
                </h2>

                <div style={{ display: 'flex', flexDirection: 'column', gap: '1.5rem' }}>
                  {contactInfo.map((info, index) => (
                    <div
                      key={index}
                      style={{
                        display: 'flex',
                        alignItems: 'center',
                        gap: '1rem',
                        padding: '1rem',
                        background: 'var(--card-bg)',
                        borderRadius: '8px',
                        border: '1px solid var(--border-color)',
                      }}
                    >
                      <span style={{ fontSize: '1.5rem' }}>{info.icon}</span>
                      <div>
                        <div style={{
                          color: 'var(--text-primary)',
                          fontWeight: 600,
                          marginBottom: '0.25rem',
                        }}>
                          {info.title}
                        </div>
                        {info.link ? (
                          <a
                            href={info.link}
                            style={{
                              color: 'var(--accent-color)',
                              textDecoration: 'none',
                            }}
                          >
                            {info.content}
                          </a>
                        ) : (
                          <div style={{ color: 'var(--text-secondary)' }}>
                            {info.content}
                          </div>
                        )}
                      </div>
                    </div>
                  ))}
                </div>
              </div>

              {/* Social Media */}
              <div className="card">
                <h3 style={{
                  color: 'var(--text-primary)',
                  marginBottom: '1.5rem',
                  fontSize: '1.5rem',
                }}>
                  Follow Me
                </h3>
                <p style={{
                  color: 'var(--text-secondary)',
                  marginBottom: '2rem',
                  lineHeight: 1.6,
                }}>
                  Stay connected and follow my musical journey on social media
                </p>
                <div style={{
                  display: 'grid',
                  gridTemplateColumns: 'repeat(auto-fit, minmax(120px, 1fr))',
                  gap: '1rem',
                }}>
                  {socialLinks.map((social) => (
                    <motion.a
                      key={social.name}
                      href={social.url}
                      target="_blank"
                      rel="noopener noreferrer"
                      whileHover={{ scale: 1.05 }}
                      whileTap={{ scale: 0.95 }}
                      style={{
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        gap: '0.5rem',
                        padding: '0.8rem',
                        background: 'var(--card-bg)',
                        border: '1px solid var(--border-color)',
                        borderRadius: '8px',
                        color: 'var(--text-primary)',
                        textDecoration: 'none',
                        transition: 'all 0.3s ease',
                      }}
                      onMouseEnter={(e) => {
                        e.target.style.background = 'var(--accent-color)';
                        e.target.style.borderColor = 'var(--accent-color)';
                        e.target.style.color = 'white';
                      }}
                      onMouseLeave={(e) => {
                        e.target.style.background = 'var(--card-bg)';
                        e.target.style.borderColor = 'var(--border-color)';
                        e.target.style.color = 'var(--text-primary)';
                      }}
                    >
                      <span style={{ fontSize: '1.2rem' }}>{social.icon}</span>
                      <span style={{ fontSize: '0.9rem' }}>{social.name}</span>
                    </motion.a>
                  ))}
                </div>
              </div>
            </motion.div>
          </div>
        </div>
      </section>
    </div>
  );
};

export default Contact;
