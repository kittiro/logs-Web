const express = require('express');
const router = express.Router();
const LogReader = require('../services/logReader');
const { convertToCSV } = require('../utils/formatters');

const logReader = new LogReader(process.env.LOG_DIR);

/**
 * GET /api/logs
 * Get logs with optional filtering and pagination
 * n8n compatible endpoint
 */
router.get('/logs', (req, res) => {
  try {
    const {
      date,
      ip,
      url,
      method,
      username,
      limit = process.env.DEFAULT_LIMIT || 100,
      offset = 0
    } = req.query;

    // Read logs
    let logs = date ? logReader.readLogsByDate(date) : logReader.readLogs();

    // Apply filters
    logs = logReader.filterLogs(logs, { ip, url, method, username });

    // Get total before pagination
    const total = logs.length;

    // Paginate
    const paginatedLogs = logReader.paginateLogs(logs, parseInt(limit), parseInt(offset));

    // n8n compatible response format
    res.json({
      success: true,
      data: paginatedLogs,
      pagination: {
        total,
        limit: parseInt(limit),
        offset: parseInt(offset),
        hasMore: (parseInt(offset) + parseInt(limit)) < total
      },
      filters: { date, ip, url, method, username },
      timestamp: new Date().toISOString()
    });
  } catch (error) {
    res.status(500).json({
      success: false,
      error: error.message,
      timestamp: new Date().toISOString()
    });
  }
});

/**
 * GET /api/logs/dates
 * Get available log dates
 * Useful for n8n to know which dates have logs
 */
router.get('/logs/dates', (req, res) => {
  try {
    const dates = logReader.getAvailableDates();
    res.json({
      success: true,
      data: dates,
      count: dates.length,
      timestamp: new Date().toISOString()
    });
  } catch (error) {
    res.status(500).json({
      success: false,
      error: error.message,
      timestamp: new Date().toISOString()
    });
  }
});

/**
 * GET /api/logs/download
 * Download logs in different formats
 */
router.get('/logs/download', (req, res) => {
  try {
    const { format = 'json', date, ip, url, method, username } = req.query;

    // Read and filter logs
    let logs = date ? logReader.readLogsByDate(date) : logReader.readLogs();
    logs = logReader.filterLogs(logs, { ip, url, method, username });

    const filename = `logs-${date || 'today'}.${format}`;

    switch (format.toLowerCase()) {
      case 'csv':
        res.setHeader('Content-Type', 'text/csv');
        res.setHeader('Content-Disposition', `attachment; filename="${filename}"`);
        res.send(convertToCSV(logs));
        break;

      case 'txt':
        res.setHeader('Content-Type', 'text/plain');
        res.setHeader('Content-Disposition', `attachment; filename="${filename}"`);
        res.send(logs.map(log => log.raw).join('\n'));
        break;

      case 'json':
      default:
        res.setHeader('Content-Type', 'application/json');
        res.setHeader('Content-Disposition', `attachment; filename="${filename}"`);
        res.json({
          success: true,
          data: logs,
          count: logs.length,
          exportedAt: new Date().toISOString()
        });
        break;
    }
  } catch (error) {
    res.status(500).json({
      success: false,
      error: error.message,
      timestamp: new Date().toISOString()
    });
  }
});

/**
 * GET /api/logs/stats
 * Get log statistics
 * Perfect for n8n dashboards and monitoring
 */
router.get('/logs/stats', (req, res) => {
  try {
    const { date } = req.query;
    const logs = date ? logReader.readLogsByDate(date) : logReader.readLogs();

    // Calculate statistics
    const stats = {
      total: logs.length,
      uniqueIPs: [...new Set(logs.map(log => log.ip))].length,
      methods: {},
      topURLs: {},
      users: {},
      timeRange: {
        first: logs[0]?.timestamp || null,
        last: logs[logs.length - 1]?.timestamp || null
      }
    };

    // Count by method
    logs.forEach(log => {
      if (log.method) {
        stats.methods[log.method] = (stats.methods[log.method] || 0) + 1;
      }
      if (log.url) {
        stats.topURLs[log.url] = (stats.topURLs[log.url] || 0) + 1;
      }
      if (log.username) {
        stats.users[log.username] = (stats.users[log.username] || 0) + 1;
      }
    });

    // Sort top URLs
    stats.topURLs = Object.entries(stats.topURLs)
      .sort((a, b) => b[1] - a[1])
      .slice(0, 10)
      .reduce((obj, [key, val]) => ({ ...obj, [key]: val }), {});

    res.json({
      success: true,
      data: stats,
      date: date || 'today',
      timestamp: new Date().toISOString()
    });
  } catch (error) {
    res.status(500).json({
      success: false,
      error: error.message,
      timestamp: new Date().toISOString()
    });
  }
});

/**
 * POST /api/webhook/n8n
 * Webhook endpoint for n8n to send data or trigger actions
 */
router.post('/webhook/n8n', (req, res) => {
  try {
    console.log('n8n webhook received:', req.body);
    
    // Process webhook data
    const { action, filters, config } = req.body;

    let result = {};

    switch (action) {
      case 'getLogs':
        const logs = logReader.readLogs(filters?.date);
        const filtered = logReader.filterLogs(logs, filters);
        result = {
          success: true,
          data: filtered,
          count: filtered.length
        };
        break;

      case 'getStats':
        const statsLogs = logReader.readLogs(filters?.date);
        result = {
          success: true,
          data: {
            total: statsLogs.length,
            uniqueIPs: [...new Set(statsLogs.map(log => log.ip))].length
          }
        };
        break;

      default:
        result = {
          success: false,
          error: 'Unknown action'
        };
    }

    res.json({
      ...result,
      timestamp: new Date().toISOString(),
      webhook: 'n8n'
    });
  } catch (error) {
    res.status(500).json({
      success: false,
      error: error.message,
      timestamp: new Date().toISOString()
    });
  }
});

module.exports = router;
