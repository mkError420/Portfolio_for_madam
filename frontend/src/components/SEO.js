import React from 'react';
import { Helmet } from 'react-helmet-async';

const SEO = ({ 
  title, 
  description, 
  keywords, 
  image, 
  url, 
  type = 'website' 
}) => {
  const siteTitle = 'Artist Name - Singer Portfolio';
  const fullTitle = title ? `${title} | ${siteTitle}` : siteTitle;
  const siteDescription = description || 'Professional singer and performer, bringing soulful music to audiences worldwide.';
  const siteImage = image || 'https://via.placeholder.com/1200x630/1a1a1a/ffffff?text=Artist+Portfolio';
  const siteUrl = url || 'https://artistname.com';

  return (
    <Helmet>
      {/* Basic Meta Tags */}
      <title>{fullTitle}</title>
      <meta name="description" content={siteDescription} />
      <meta name="keywords" content={keywords || 'singer, musician, artist, music, portfolio, performances, albums'} />
      <meta name="author" content="Artist Name" />
      <meta name="robots" content="index, follow" />
      <meta name="language" content="English" />

      {/* Open Graph / Facebook */}
      <meta property="og:type" content={type} />
      <meta property="og:url" content={siteUrl} />
      <meta property="og:title" content={fullTitle} />
      <meta property="og:description" content={siteDescription} />
      <meta property="og:image" content={siteImage} />
      <meta property="og:image:width" content="1200" />
      <meta property="og:image:height" content="630" />
      <meta property="og:site_name" content={siteTitle} />

      {/* Twitter */}
      <meta property="twitter:card" content="summary_large_image" />
      <meta property="twitter:url" content={siteUrl} />
      <meta property="twitter:title" content={fullTitle} />
      <meta property="twitter:description" content={siteDescription} />
      <meta property="twitter:image" content={siteImage} />

      {/* Additional SEO */}
      <link rel="canonical" href={siteUrl} />
      <meta name="theme-color" content="#1a1a1a" />
      
      {/* Structured Data */}
      <script type="application/ld+json">
        {JSON.stringify({
          "@context": "https://schema.org",
          "@type": "Person",
          "name": "Artist Name",
          "url": siteUrl,
          "image": siteImage,
          "description": siteDescription,
          "sameAs": [
            "https://facebook.com/artistname",
            "https://twitter.com/artistname",
            "https://instagram.com/artistname",
            "https://youtube.com/artistname"
          ],
          "jobTitle": "Singer, Songwriter, Performer",
          "knowsAbout": ["Music", "Singing", "Songwriting", "Performance"]
        })}
      </script>
    </Helmet>
  );
};

export default SEO;
