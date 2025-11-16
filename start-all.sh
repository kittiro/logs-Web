#!/bin/bash

echo "🚀 Starting WebManga Demo with Node.js Log API..."

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Start Node.js API in background
echo -e "${BLUE}📡 Starting Node.js Log API on port 3000...${NC}"
cd node-api && npm start &
NODE_PID=$!

# Wait a bit for Node.js to start
sleep 2

# Start Laravel
echo -e "${GREEN}🎨 Starting Laravel application on port 8000...${NC}"
cd ..
php artisan serve --host=0.0.0.0 --port=8000 &
LARAVEL_PID=$!

echo ""
echo -e "${GREEN}✅ All services started!${NC}"
echo ""
echo "📱 Laravel App: http://localhost:8000"
echo "📡 Node.js API: http://localhost:3000"
echo "📊 API Health: http://localhost:3000/health"
echo "📝 API Logs: http://localhost:3000/api/logs"
echo ""
echo "Press Ctrl+C to stop all services"

# Wait for Ctrl+C
trap "echo 'Stopping services...'; kill $NODE_PID $LARAVEL_PID; exit" INT
wait
