#!/bin/bash

echo "🚀 Starting Node.js Log API Server..."
echo ""

# Check if .env exists
if [ ! -f .env ]; then
    echo "❌ Error: .env file not found!"
    exit 1
fi

# Check if node_modules exists
if [ ! -d node_modules ]; then
    echo "📦 Installing dependencies..."
    npm install
fi

echo "✅ Starting server on port 3000..."
echo "📍 API Endpoints:"
echo "   - http://localhost:3000/"
echo "   - http://localhost:3000/health"
echo "   - http://localhost:3000/api/logs"
echo "   - http://localhost:3000/webhook/line"
echo ""
echo "Press Ctrl+C to stop the server"
echo ""

node server.js
