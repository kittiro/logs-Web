require('dotenv').config();
const express = require('express');
const cors = require('cors');
const logRoutes = require('./routes/logs');
const lineRoutes = require('./routes/line');

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(cors({ origin: process.env.CORS_ORIGIN || '*' }));

// Request logging (before body parsers for LINE webhook)
app.use((req, res, next) => {
  console.log(`[${new Date().toISOString()}] ${req.method} ${req.path}`);
  next();
});

// Root route - must be before other routes
app.get('/', (req, res) => {
  res.json({
    name: 'Log API Server',
    version: '1.0.0',
    status: 'running',
    endpoints: {
      health: '/health',
      logs: '/api/logs',
      stats: '/api/logs/stats',
      dates: '/api/logs/dates',
      download: '/api/logs/download',
      lineWebhook: '/webhook/line',
      n8nWebhook: '/api/webhook/n8n'
    },
    timestamp: new Date().toISOString()
  });
});

// Health check
app.get('/health', (req, res) => {
  res.json({
    status: 'ok',
    timestamp: new Date().toISOString(),
    uptime: process.uptime(),
    version: '1.0.0'
  });
});

// Routes
app.use('/api', logRoutes);
app.use('/webhook', lineRoutes);  // LINE webhook routes

// 404 handler
app.use((req, res) => {
  res.status(404).json({
    error: 'Not Found',
    message: `Route ${req.method} ${req.path} not found`
  });
});

// Error handler
app.use((err, req, res, next) => {
  console.error('Error:', err);
  res.status(err.status || 500).json({
    error: err.message || 'Internal Server Error',
    ...(process.env.NODE_ENV === 'development' && { stack: err.stack })
  });
});

// Start server
app.listen(PORT, () => {
  console.log(`🚀 Log API Server running on port ${PORT}`);
  console.log(`📁 Log directory: ${process.env.LOG_DIR}`);
  console.log(`🌐 CORS origin: ${process.env.CORS_ORIGIN}`);
});
