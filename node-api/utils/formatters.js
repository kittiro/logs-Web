/**
 * Convert logs array to CSV format
 */
function convertToCSV(logs) {
  if (!logs || logs.length === 0) {
    return 'No data';
  }

  // CSV headers
  const headers = ['Timestamp', 'IP', 'Method', 'URL', 'User Agent', 'Username', 'User ID'];
  const csvRows = [headers.join(',')];

  // Add data rows
  logs.forEach(log => {
    const row = [
      log.timestamp || '',
      log.ip || '',
      log.method || '',
      log.url || '',
      `"${(log.userAgent || '').replace(/"/g, '""')}"`, // Escape quotes
      log.username || '',
      log.userId || ''
    ];
    csvRows.push(row.join(','));
  });

  return csvRows.join('\n');
}

/**
 * Format logs for n8n workflow
 */
function formatForN8N(logs) {
  return logs.map(log => ({
    json: {
      timestamp: log.timestamp,
      ip: log.ip,
      method: log.method,
      url: log.url,
      userAgent: log.userAgent,
      username: log.username,
      userId: log.userId
    }
  }));
}

module.exports = {
  convertToCSV,
  formatForN8N
};
