// Unique Button Styles Component
export const ButtonStyles = `
  /* Base Button Styles */
  .btn {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.8rem 2rem;
    font-size: 1rem;
    font-weight: 600;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    text-decoration: none;
    border: none;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.23, 1, 0.320, 1);
    overflow: hidden;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    z-index: 1;
  }

  .btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    transform: translateX(-100%);
    transition: transform 0.6s;
    z-index: -1;
  }

  .btn:hover::before {
    transform: translateX(100%);
  }

  .btn::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
    z-index: -1;
  }

  .btn:hover::after {
    width: 300px;
    height: 300px;
  }

  /* Primary Button - Gradient with Glow */
  .btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4), 0 0 20px rgba(102, 126, 234, 0.2);
  }

  .btn-primary:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6), 0 0 30px rgba(102, 126, 234, 0.4);
    background: linear-gradient(135deg, #764ba2 0%, #f093fb 50%, #667eea 100%);
  }

  /* Secondary Button - Glass Morphism */
  .btn-secondary {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    box-shadow: 0 4px 15px rgba(255, 255, 255, 0.1);
  }

  .btn-secondary:hover {
    transform: translateY(-2px) scale(1.02);
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.4);
    box-shadow: 0 8px 25px rgba(255, 255, 255, 0.2);
  }

  /* Success Button - Green Gradient */
  .btn-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(17, 153, 142, 0.4), 0 0 20px rgba(17, 153, 142, 0.2);
  }

  .btn-success:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 8px 25px rgba(17, 153, 142, 0.6), 0 0 30px rgba(17, 153, 142, 0.4);
    background: linear-gradient(135deg, #38ef7d 0%, #11998e 100%);
  }

  /* Danger Button - Red Gradient */
  .btn-danger {
    background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(235, 51, 73, 0.4), 0 0 20px rgba(235, 51, 73, 0.2);
  }

  .btn-danger:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 8px 25px rgba(235, 51, 73, 0.6), 0 0 30px rgba(235, 51, 73, 0.4);
    background: linear-gradient(135deg, #f45c43 0%, #eb3349 100%);
  }

  /* Warning Button - Orange Gradient */
  .btn-warning {
    background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(242, 153, 74, 0.4), 0 0 20px rgba(242, 153, 74, 0.2);
  }

  .btn-warning:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 8px 25px rgba(242, 153, 74, 0.6), 0 0 30px rgba(242, 153, 74, 0.4);
    background: linear-gradient(135deg, #f2c94c 0%, #f2994a 100%);
  }

  /* Info Button - Blue Gradient */
  .btn-info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(79, 172, 254, 0.4), 0 0 20px rgba(79, 172, 254, 0.2);
  }

  .btn-info:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 8px 25px rgba(79, 172, 254, 0.6), 0 0 30px rgba(79, 172, 254, 0.4);
    background: linear-gradient(135deg, #00f2fe 0%, #4facfe 100%);
  }

  /* Dark Button - Elegant Dark */
  .btn-dark {
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 50%, #1a1a1a 100%);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4), 0 0 20px rgba(0, 0, 0, 0.2);
  }

  .btn-dark:hover {
    transform: translateY(-2px) scale(1.02);
    background: linear-gradient(135deg, #2d2d2d 0%, #1a1a1a 50%, #2d2d2d 100%);
    border-color: rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.6), 0 0 30px rgba(0, 0, 0, 0.4);
  }

  /* Outline Button - Minimalist */
  .btn-outline {
    background: transparent;
    color: #667eea;
    border: 2px solid #667eea;
    box-shadow: none;
  }

  .btn-outline:hover {
    background: #667eea;
    color: white;
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
  }

  /* Ghost Button - Subtle */
  .btn-ghost {
    background: transparent;
    color: rgba(255, 255, 255, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: none;
  }

  .btn-ghost:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border-color: rgba(255, 255, 255, 0.4);
    transform: translateY(-1px);
  }

  /* Gradient Text Button - Unique */
  .btn-gradient-text {
    background: linear-gradient(135deg, #667eea, #764ba2, #f093fb);
    background-size: 200% 200%;
    animation: gradientShift 3s ease infinite;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    border: 2px solid;
    border-image: linear-gradient(135deg, #667eea, #764ba2, #f093fb) 1;
    color: transparent;
  }

  .btn-gradient-text:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
  }

  /* Neon Button - Cyberpunk Style */
  .btn-neon {
    background: #0a0a0a;
    color: #00ff88;
    border: 2px solid #00ff88;
    box-shadow: 0 0 10px #00ff88, inset 0 0 10px rgba(0, 255, 136, 0.1);
    text-shadow: 0 0 10px #00ff88;
  }

  .btn-neon:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 0 20px #00ff88, 0 0 40px #00ff88, inset 0 0 20px rgba(0, 255, 136, 0.2);
    background: #00ff88;
    color: #0a0a0a;
  }

  /* Liquid Button - Fluid Animation */
  .btn-liquid {
    background: linear-gradient(45deg, #667eea, #764ba2, #f093fb);
    background-size: 300% 300%;
    animation: liquidGradient 4s ease infinite;
    color: white;
    position: relative;
    overflow: hidden;
  }

  .btn-liquid::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
    animation: liquidBubble 3s ease-in-out infinite;
  }

  .btn-liquid:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
  }

  /* Pulse Button - Heartbeat Effect */
  .btn-pulse {
    background: linear-gradient(135deg, #ff6b6b, #4ecdc4);
    color: white;
    animation: pulse 2s infinite;
  }

  .btn-pulse:hover {
    animation: pulse 1s infinite;
    transform: translateY(-2px) scale(1.02);
  }

  /* Size Variants */
  .btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    border-radius: 25px;
  }

  .btn-lg {
    padding: 1rem 3rem;
    font-size: 1.125rem;
    border-radius: 60px;
  }

  .btn-xl {
    padding: 1.25rem 4rem;
    font-size: 1.25rem;
    border-radius: 75px;
  }

  /* Button States */
  .btn:active {
    transform: translateY(0) scale(0.98);
  }

  .btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
  }

  .btn:disabled:hover {
    transform: none;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
  }

  /* Animations */
  @keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }

  @keyframes liquidGradient {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }

  @keyframes liquidBubble {
    0%, 100% { transform: translate(-50%, -50%) scale(0); }
    50% { transform: translate(-50%, -50%) scale(1); }
  }

  @keyframes pulse {
    0% { box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4); }
    50% { box-shadow: 0 4px 25px rgba(102, 126, 234, 0.8); }
    100% { box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4); }
  }

  /* Responsive Design */
  @media (max-width: 768px) {
    .btn {
      padding: 0.7rem 1.5rem;
      font-size: 0.9rem;
    }
    
    .btn-sm {
      padding: 0.4rem 0.8rem;
      font-size: 0.8rem;
    }
    
    .btn-lg {
      padding: 0.9rem 2rem;
      font-size: 1rem;
    }
    
    .btn-xl {
      padding: 1rem 2.5rem;
      font-size: 1.1rem;
    }
  }

  /* Icon Button */
  .btn-icon {
    width: 50px;
    height: 50px;
    padding: 0;
    border-radius: 50%;
    font-size: 1.2rem;
  }

  .btn-icon-sm {
    width: 40px;
    height: 40px;
    font-size: 1rem;
  }

  .btn-icon-lg {
    width: 60px;
    height: 60px;
    font-size: 1.4rem;
  }

  /* Loading State */
  .btn-loading {
    position: relative;
    color: transparent !important;
    pointer-events: none;
  }

  .btn-loading::after {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    top: 50%;
    left: 50%;
    margin-left: -10px;
    margin-top: -10px;
    border: 2px solid transparent;
    border-top-color: currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    to { transform: rotate(360deg); }
  }
`;

export default ButtonStyles;
