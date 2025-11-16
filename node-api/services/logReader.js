const fs = require('fs');
const path = require('path');

class LogReader {
  constructor(logDir) {
    this.logDir = path.resolve(__dirname, logDir || '../storage/logs');
  }

  /**
   * Parse a single log line into an object
   */
  parseLogLine(line) {
    if (!line || line.trim() === '') return null;

    // Expected format: [2025-09-25 10:30:45] IP: 192.168.1.1 | Method: GET | URL: /dashboard | User-Agent: Mozilla... | User: admin (ID: 1)
    const timestampMatch = line.match(/\[([^\]]+)\]/);
    const ipMatch = line.match(/IP: ([^\s|]+)/);
    const methodMatch = line.match(/Method: ([^\s|]+)/);
    const urlMatch = line.match(/URL: ([^\s|]+)/);
    const userAgentMatch = line.match(/User-Agent: ([^|]+)/);
    const userMatch = line.match(/User: ([^(]+)\(ID: (\d+)\)/);

    return {
      timestamp: timestampMatch ? timestampMatch[1].trim() : null,
      ip: ipMatch ? ipMatch[1].trim() : null,
      method: methodMatch ? methodMatch[1].trim() : null,
      url: urlMatch ? urlMatch[1].trim() : null,
      userAgent: userAgentMatch ? userAgentMatch[1].trim() : null,
      username: userMatch ? userMatch[1].trim() : 'Guest',
      userId: userMatch ? userMatch[2].trim() : null,
      raw: line
    };
  }

  /**
   * Get log file path for a specific date
   */
  getLogFilePath(date) {
    const filename = `web-access-${date}.log`;
    return path.join(this.logDir, filename);
  }

  /**
   * Read logs from a specific date
   */
  readLogsByDate(date) {
    const filePath = this.getLogFilePath(date);
    
    if (!fs.existsSync(filePath)) {
      return [];
    }

    try {
      const content = fs.readFileSync(filePath, 'utf8');
      const lines = content.split('\n');
      return lines
        .map(line => this.parseLogLine(line))
        .filter(log => log !== null);
    } catch (error) {
      console.error(`Error reading log file ${filePath}:`, error);
      throw new Error(`Failed to read log file: ${error.message}`);
    }
  }

  /**
   * Get all available log dates
   */
  getAvailableDates() {
    try {
      const files = fs.readdirSync(this.logDir);
      const logFiles = files.filter(f => f.startsWith('web-access-') && f.endsWith('.log'));
      return logFiles.map(f => f.replace('web-access-', '').replace('.log', ''));
    } catch (error) {
      console.error('Error reading log directory:', error);
      return [];
    }
  }

  /**
   * Read all logs or logs from today
   */
  readLogs(date = null) {
    if (date) {
      return this.readLogsByDate(date);
    }

    // If no date specified, read today's logs
    const today = new Date().toISOString().split('T')[0];
    return this.readLogsByDate(today);
  }

  /**
   * Filter logs by criteria
   */
  filterLogs(logs, filters = {}) {
    let filtered = [...logs];

    if (filters.ip) {
      filtered = filtered.filter(log => log.ip && log.ip.includes(filters.ip));
    }

    if (filters.url) {
      filtered = filtered.filter(log => log.url && log.url.includes(filters.url));
    }

    if (filters.method) {
      filtered = filtered.filter(log => log.method && log.method.toUpperCase() === filters.method.toUpperCase());
    }

    if (filters.username) {
      filtered = filtered.filter(log => log.username && log.username.toLowerCase().includes(filters.username.toLowerCase()));
    }

    return filtered;
  }

  /**
   * Paginate logs
   */
  paginateLogs(logs, limit = 100, offset = 0) {
    return logs.slice(offset, offset + limit);
  }
}

module.exports = LogReader;
