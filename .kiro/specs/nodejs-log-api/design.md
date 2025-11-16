# Design Document

## Overview

This design creates a lightweight Node.js Express API server that reads web access logs from the Laravel application's storage directory and provides RESTful endpoints for viewing, filtering, and downloading logs. The API operates independently of the Laravel application, making it suitable for offline log analysis.

## Architecture

### Technology Stack
- **Runtime**: Node.js (v18+)
- **Framework**: Express.js
- **File System**: Node.js fs module for reading logs
- **CORS**: Enabled for cross-origin requests
- **Port**: Configurable (default: 3000)

### API Structure
```
/api/logs              - GET all logs with optional filters
/api/logs/download     - GET logs in different formats
/api/logs/stats        - GET log statistics
/api/health            - GET API health check
```

## Components and Interfaces

### 1. Express Server Component
- Initialize Express application
- Configure middleware (CORS, JSON parser)
- Set up routes
- Handle errors

### 2. Log Reader Service
- Read log files from storage directory
- Parse log entries
- Filter logs by criteria
- Format logs for output

### 3. API Endpoints

**GET /api/logs**
- Query params: date, ip, url, limit, offset
- Response: JSON array of log entries

**GET /api/logs/download**
- Query params: format (json|csv|txt), date, ip, url
- Response: File download

**GET /api/logs/stats**
- Response: Statistics (total requests, unique IPs, etc.)

**GET /api/health**
- Response: API status

## Data Models

### Log Entry Model
```javascript
{
  timestamp: "2025-09-25 10:30:45",
  ip: "192.168.1.1",
  method: "GET",
  url: "/dashboard",
  userAgent: "Mozilla/5.0...",
  userId: "1",
  username: "admin"
}
```

## Error Handling

- 404: Log file not found
- 500: File read errors
- 400: Invalid query parameters
- CORS errors handling

## Testing Strategy

- Test log file reading
- Test filtering functionality
- Test format conversion
- Test error scenarios
