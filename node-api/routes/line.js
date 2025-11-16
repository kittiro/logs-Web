const express = require('express');
const router = express.Router();
const line = require('@line/bot-sdk');
const LogReader = require('../services/logReader');

const logReader = new LogReader(process.env.LOG_DIR);

// LINE configuration
const config = {
  channelSecret: process.env.LINE_CHANNEL_SECRET,
  channelAccessToken: process.env.LINE_CHANNEL_ACCESS_TOKEN
};

const client = new line.messagingApi.MessagingApiClient({
  channelAccessToken: config.channelAccessToken
});

/**
 * Handle LINE webhook events
 */
router.post('/line', line.middleware(config), async (req, res) => {
  try {
    const events = req.body.events;
    
    await Promise.all(events.map(handleEvent));
    
    res.json({ success: true });
  } catch (error) {
    console.error('LINE webhook error:', error);
    res.status(500).json({ error: error.message });
  }
});

/**
 * Handle individual LINE event
 */
async function handleEvent(event) {
  if (event.type !== 'message' || event.message.type !== 'text') {
    return null;
  }

  const userMessage = event.message.text.trim();
  const replyToken = event.replyToken;

  try {
    const response = await processCommand(userMessage);
    return client.replyMessage({
      replyToken: replyToken,
      messages: [{ type: 'text', text: response }]
    });
  } catch (error) {
    console.error('Error processing command:', error);
    return client.replyMessage({
      replyToken: replyToken,
      messages: [{ type: 'text', text: '❌ เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง' }]
    });
  }
}

/**
 * Process user commands
 */
async function processCommand(message) {
  const command = message.toLowerCase();

  // Help command
  if (command === '/help' || command === 'help') {
    return `📚 คำสั่งที่ใช้ได้:

/stats - สถิติ logs วันนี้
/logs - logs ล่าสุด 5 รายการ
/logs [จำนวน] - logs ล่าสุด
/dates - วันที่ที่มี logs
/ip [IP] - ค้นหาจาก IP
/url [URL] - ค้นหาจาก URL
/help - แสดงคำสั่งนี้`;
  }

  // Stats command
  if (command === '/stats') {
    const logs = logReader.readLogs();
    const stats = {
      total: logs.length,
      uniqueIPs: [...new Set(logs.map(log => log.ip))].length,
      methods: {}
    };

    logs.forEach(log => {
      if (log.method) {
        stats.methods[log.method] = (stats.methods[log.method] || 0) + 1;
      }
    });

    return `📊 สถิติ Logs (วันนี้)

📈 Total: ${stats.total.toLocaleString()} requests
👥 Unique IPs: ${stats.uniqueIPs}
📝 GET: ${stats.methods.GET || 0} | POST: ${stats.methods.POST || 0}`;
  }

  // Logs command
  if (command.startsWith('/logs')) {
    const parts = command.split(' ');
    const limit = parts[1] ? parseInt(parts[1]) : 5;
    
    const logs = logReader.readLogs();
    const latest = logs.slice(-limit).reverse();

    if (latest.length === 0) {
      return '📝 ไม่พบ logs';
    }

    let response = `📝 Logs ล่าสุด ${latest.length} รายการ:\n\n`;
    latest.forEach((log, index) => {
      response += `${index + 1}. [${log.timestamp}]\n`;
      response += `   ${log.ip} → ${log.method} ${log.url}\n`;
      response += `   User: ${log.username}\n\n`;
    });

    return response.trim();
  }

  // Dates command
  if (command === '/dates') {
    const dates = logReader.getAvailableDates();
    
    if (dates.length === 0) {
      return '📅 ไม่พบ log files';
    }

    return `📅 วันที่ที่มี logs:\n\n${dates.map((d, i) => `${i + 1}. ${d}`).join('\n')}`;
  }

  // IP search command
  if (command.startsWith('/ip ')) {
    const ip = command.substring(4).trim();
    const logs = logReader.readLogs();
    const filtered = logs.filter(log => log.ip && log.ip.includes(ip));

    if (filtered.length === 0) {
      return `🔍 ไม่พบ logs จาก IP: ${ip}`;
    }

    const latest = filtered.slice(-5).reverse();
    let response = `🔍 Logs จาก IP: ${ip}\n`;
    response += `พบ ${filtered.length} requests\n\n`;
    
    latest.forEach((log, index) => {
      response += `${index + 1}. [${log.timestamp}]\n`;
      response += `   ${log.method} ${log.url}\n`;
      response += `   User: ${log.username}\n\n`;
    });

    return response.trim();
  }

  // URL search command
  if (command.startsWith('/url ')) {
    const url = command.substring(5).trim();
    const logs = logReader.readLogs();
    const filtered = logs.filter(log => log.url && log.url.includes(url));

    if (filtered.length === 0) {
      return `🔍 ไม่พบ logs สำหรับ URL: ${url}`;
    }

    const latest = filtered.slice(-5).reverse();
    let response = `🔍 Logs สำหรับ URL: ${url}\n`;
    response += `พบ ${filtered.length} requests\n\n`;
    
    latest.forEach((log, index) => {
      response += `${index + 1}. [${log.timestamp}]\n`;
      response += `   ${log.ip} → ${log.method}\n`;
      response += `   User: ${log.username}\n\n`;
    });

    return response.trim();
  }

  // Default response
  return `ไม่เข้าใจคำสั่ง "${message}"\nพิมพ์ /help เพื่อดูคำสั่งทั้งหมด`;
}

/**
 * Push message to user (for notifications)
 */
router.post('/line/push', async (req, res) => {
  try {
    const { userId, message } = req.body;

    if (!userId || !message) {
      return res.status(400).json({ error: 'userId and message are required' });
    }

    await client.pushMessage({
      to: userId,
      messages: [{ type: 'text', text: message }]
    });

    res.json({ success: true, message: 'Message sent' });
  } catch (error) {
    console.error('Push message error:', error);
    res.status(500).json({ error: error.message });
  }
});

module.exports = router;
