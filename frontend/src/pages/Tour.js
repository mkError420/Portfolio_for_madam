import React, { useState } from 'react';
import { motion } from 'framer-motion';

const Tour = () => {
  const [filter, setFilter] = useState('upcoming');

  const tourDates = [
    {
      id: 1,
      date: '2024-03-15',
      venue: 'Madison Square Garden',
      city: 'New York',
      country: 'USA',
      status: 'upcoming',
      ticketUrl: '#',
      price: '$75 - $250',
      special: 'Sold Out'
    },
    {
      id: 2,
      date: '2024-03-18',
      venue: 'Royal Albert Hall',
      city: 'London',
      country: 'UK',
      status: 'upcoming',
      ticketUrl: '#',
      price: '$60 - $200'
    },
    {
      id: 3,
      date: '2024-03-22',
      venue: 'Olympia',
      city: 'Paris',
      country: 'France',
      status: 'upcoming',
      ticketUrl: '#',
      price: '$55 - $180'
    },
    {
      id: 4,
      date: '2024-03-25',
      venue: 'Tokyo Dome',
      city: 'Tokyo',
      country: 'Japan',
      status: 'upcoming',
      ticketUrl: '#',
      price: '$80 - $300'
    },
    {
      id: 5,
      date: '2024-02-28',
      venue: 'Sydney Opera House',
      city: 'Sydney',
      country: 'Australia',
      status: 'past',
      ticketUrl: null,
      price: '$70 - $220'
    },
    {
      id: 6,
      date: '2024-02-20',
      venue: 'Red Rocks Amphitheatre',
      city: 'Colorado',
      country: 'USA',
      status: 'past',
      ticketUrl: null,
      price: '$65 - $190'
    }
  ];

  const filteredDates = filter === 'all' 
    ? tourDates 
    : tourDates.filter(date => date.status === filter);

  const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
      weekday: 'short',
      month: 'short',
      day: 'numeric',
      year: 'numeric'
    });
  };

  const isSoldOut = (tourDate) => {
    return tourDate.special === 'Sold Out';
  };

  return (
    <div className="tour">
      {/* Hero Section */}
      <section className="tour-hero" style={{
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
              Tour & Events
            </h1>
            <p style={{
              fontSize: '1.2rem',
              color: 'var(--text-secondary)',
              maxWidth: '600px',
              margin: '0 auto',
              lineHeight: 1.6,
            }}>
              Join me live on stage for unforgettable musical experiences around the world
            </p>
          </motion.div>
        </div>
      </section>

      {/* Tour Banner */}
      <section className="tour-banner" style={{
        padding: '3rem 0',
        background: 'var(--primary-color)',
      }}>
        <div className="container">
          <motion.div
            initial={{ opacity: 0, scale: 0.9 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ duration: 0.8 }}
            className="card"
            style={{
              textAlign: 'center',
              padding: '3rem',
              background: 'var(--gradient-primary)',
            }}
          >
            <h2 style={{
              color: 'var(--text-primary)',
              marginBottom: '1rem',
              fontSize: '2rem',
            }}>
              World Tour 2024
            </h2>
            <p style={{
              color: 'var(--text-primary)',
              fontSize: '1.1rem',
              marginBottom: '2rem',
              opacity: 0.9,
            }}>
              Experience the magic of live music in cities across the globe
            </p>
            <div style={{
              display: 'flex',
              justifyContent: 'center',
              gap: '2rem',
              flexWrap: 'wrap',
            }}>
              <div>
                <div style={{
                  fontSize: '2rem',
                  fontWeight: 'bold',
                  color: 'var(--text-primary)',
                }}>
                  25+
                </div>
                <div style={{ color: 'var(--text-primary)', opacity: 0.8 }}>
                  Cities
                </div>
              </div>
              <div>
                <div style={{
                  fontSize: '2rem',
                  fontWeight: 'bold',
                  color: 'var(--text-primary)',
                }}>
                  15
                </div>
                <div style={{ color: 'var(--text-primary)', opacity: 0.8 }}>
                  Countries
                </div>
              </div>
              <div>
                <div style={{
                  fontSize: '2rem',
                  fontWeight: 'bold',
                  color: 'var(--text-primary)',
                }}>
                  1M+
                </div>
                <div style={{ color: 'var(--text-primary)', opacity: 0.8 }}>
                  Fans
                </div>
              </div>
            </div>
          </motion.div>
        </div>
      </section>

      {/* Filter Tabs */}
      <section className="tour-filters" style={{
        padding: '2rem 0',
        background: 'var(--secondary-color)',
      }}>
        <div className="container">
          <div style={{
            display: 'flex',
            justifyContent: 'center',
            gap: '1rem',
            flexWrap: 'wrap',
          }}>
            <button
              onClick={() => setFilter('upcoming')}
              className={`btn ${filter === 'upcoming' ? 'btn-primary' : 'btn-secondary'}`}
            >
              Upcoming Shows
            </button>
            <button
              onClick={() => setFilter('past')}
              className={`btn ${filter === 'past' ? 'btn-primary' : 'btn-secondary'}`}
            >
              Past Shows
            </button>
            <button
              onClick={() => setFilter('all')}
              className={`btn ${filter === 'all' ? 'btn-primary' : 'btn-secondary'}`}
            >
              All Shows
            </button>
          </div>
        </div>
      </section>

      {/* Tour Dates */}
      <section className="tour-dates" style={{
        padding: '3rem 0',
        background: 'var(--primary-color)',
      }}>
        <div className="container">
          <div style={{ maxWidth: '900px', margin: '0 auto' }}>
            {filteredDates.length === 0 ? (
              <motion.div
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                style={{ textAlign: 'center', padding: '3rem' }}
              >
                <p style={{ color: 'var(--text-secondary)', fontSize: '1.1rem' }}>
                  No {filter} shows at the moment. Check back soon for updates!
                </p>
              </motion.div>
            ) : (
              filteredDates.map((tourDate, index) => (
                <motion.div
                  key={tourDate.id}
                  initial={{ opacity: 0, x: -50 }}
                  animate={{ opacity: 1, x: 0 }}
                  transition={{ duration: 0.8, delay: index * 0.1 }}
                  className="card"
                  style={{
                    display: 'grid',
                    gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))',
                    gap: '2rem',
                    alignItems: 'center',
                    padding: '2rem',
                    marginBottom: '1.5rem',
                    position: 'relative',
                  }}
                >
                  {/* Date */}
                  <div>
                    <div style={{
                      color: 'var(--accent-color)',
                      fontSize: '0.9rem',
                      fontWeight: 600,
                      textTransform: 'uppercase',
                      letterSpacing: '1px',
                      marginBottom: '0.5rem',
                    }}>
                      {new Date(tourDate.date).toLocaleDateString('en-US', { weekday: 'short' })}
                    </div>
                    <div style={{
                      color: 'var(--text-primary)',
                      fontSize: '1.3rem',
                      fontWeight: 'bold',
                    }}>
                      {new Date(tourDate.date).toLocaleDateString('en-US', { 
                        month: 'short', 
                        day: 'numeric' 
                      })}
                    </div>
                    <div style={{
                      color: 'var(--text-secondary)',
                      fontSize: '0.9rem',
                    }}>
                      {new Date(tourDate.date).getFullYear()}
                    </div>
                  </div>

                  {/* Venue Info */}
                  <div style={{ gridColumn: 'span 2' }}>
                    <h3 style={{
                      color: 'var(--text-primary)',
                      margin: '0 0 0.5rem',
                      fontSize: '1.2rem',
                    }}>
                      {tourDate.venue}
                    </h3>
                    <p style={{
                      color: 'var(--text-secondary)',
                      margin: 0,
                      fontSize: '1rem',
                    }}>
                      {tourDate.city}, {tourDate.country}
                    </p>
                  </div>

                  {/* Price */}
                  <div>
                    <div style={{
                      color: 'var(--text-primary)',
                      fontSize: '1.1rem',
                      fontWeight: 500,
                    }}>
                      {tourDate.price}
                    </div>
                  </div>

                  {/* Actions */}
                  <div style={{ textAlign: 'right' }}>
                    {tourDate.status === 'upcoming' ? (
                      isSoldOut(tourDate) ? (
                        <span style={{
                          background: 'var(--accent-color)',
                          color: 'white',
                          padding: '0.5rem 1rem',
                          borderRadius: '25px',
                          fontSize: '0.9rem',
                          fontWeight: 600,
                        }}>
                          Sold Out
                        </span>
                      ) : (
                        <a
                          href={tourDate.ticketUrl}
                          className="btn btn-primary"
                          style={{ textDecoration: 'none' }}
                        >
                          Get Tickets
                        </a>
                      )
                    ) : (
                      <span style={{
                        color: 'var(--text-muted)',
                        fontSize: '0.9rem',
                        fontStyle: 'italic',
                      }}>
                        Past Event
                      </span>
                    )}
                  </div>

                  {/* Status Badge */}
                  {tourDate.special && (
                    <div style={{
                      position: 'absolute',
                      top: '1rem',
                      right: '1rem',
                      background: 'var(--accent-color)',
                      color: 'white',
                      padding: '0.3rem 0.8rem',
                      borderRadius: '15px',
                      fontSize: '0.8rem',
                      fontWeight: 600,
                    }}>
                      {tourDate.special}
                    </div>
                  )}
                </motion.div>
              ))
            )}
          </div>
        </div>
      </section>

      {/* Tour Information */}
      <section className="tour-info" style={{
        padding: '5rem 0',
        background: 'var(--secondary-color)',
      }}>
        <div className="container">
          <div style={{
            display: 'grid',
            gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))',
            gap: '2rem',
          }}>
            <motion.div
              initial={{ opacity: 0, y: 50 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.8 }}
              viewport={{ once: true }}
              className="card"
            >
              <h3 style={{
                color: 'var(--text-primary)',
                marginBottom: '1rem',
                display: 'flex',
                alignItems: 'center',
                gap: '0.5rem',
              }}>
                🎫 Ticket Information
              </h3>
              <p style={{ color: 'var(--text-secondary)', lineHeight: 1.6 }}>
                Tickets are available through official vendors only. Be cautious of third-party sellers 
                and always verify ticket authenticity. VIP packages include meet & greet opportunities.
              </p>
            </motion.div>

            <motion.div
              initial={{ opacity: 0, y: 50 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.8, delay: 0.1 }}
              viewport={{ once: true }}
              className="card"
            >
              <h3 style={{
                color: 'var(--text-primary)',
                marginBottom: '1rem',
                display: 'flex',
                alignItems: 'center',
                gap: '0.5rem',
              }}>
                🎵 What to Expect
              </h3>
              <p style={{ color: 'var(--text-secondary)', lineHeight: 1.6 }}>
                Each show features a 2-hour performance with hits from all albums, 
                special acoustic moments, and stunning visual production. 
                Doors open 1 hour before showtime.
              </p>
            </motion.div>

            <motion.div
              initial={{ opacity: 0, y: 50 }}
              whileInView={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.8, delay: 0.2 }}
              viewport={{ once: true }}
              className="card"
            >
              <h3 style={{
                color: 'var(--text-primary)',
                marginBottom: '1rem',
                display: 'flex',
                alignItems: 'center',
                gap: '0.5rem',
              }}>
                📧 Stay Updated
              </h3>
              <p style={{ color: 'var(--text-secondary)', lineHeight: 1.6 }}>
                Sign up for the newsletter to receive exclusive presale access, 
                tour announcements, and special fan experiences. 
                Follow on social media for real-time updates.
              </p>
            </motion.div>
          </div>
        </div>
      </section>
    </div>
  );
};

export default Tour;
