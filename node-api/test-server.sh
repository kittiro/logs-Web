#!/bin/bash

echo "🧪 Testing Node.js Log API Server..."
echo ""

# Test root endpoint
echo "1️⃣ Testing GET /"
curl -s http://localhost:3000/ | jq '.' || curl -s http://localhost:3000/
echo ""
echo ""

# Test health endpoint
echo "2️⃣ Testing GET /health"
curl -s http://localhost:3000/health | jq '.' || curl -s http://localhost:3000/health
echo ""
echo ""

# Test logs endpoint
echo "3️⃣ Testing GET /api/logs"
curl -s http://localhost:3000/api/logs | jq '.' || curl -s http://localhost:3000/api/logs
echo ""
echo ""

# Test stats endpoint
echo "4️⃣ Testing GET /api/logs/stats"
curl -s http://localhost:3000/api/logs/stats | jq '.' || curl -s http://localhost:3000/api/logs/stats
echo ""
echo ""

# Test dates endpoint
echo "5️⃣ Testing GET /api/logs/dates"
curl -s http://localhost:3000/api/logs/dates | jq '.' || curl -s http://localhost:3000/api/logs/dates
echo ""
echo ""

echo "✅ Testing complete!"
