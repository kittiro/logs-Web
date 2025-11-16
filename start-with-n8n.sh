#!/bin/bash

echo "🚀 Starting WebManga Demo with Node.js Log API and n8n..."

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
PURPLE='\033[0;35m'
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

# Wait a bit for Laravel to start
sleep 2

# Start n8n
echo -e "${PURPLE}🔗 Starting n8n on port 5678...${NC}"
npx n8n &
N8N_PID=$!

echo ""
echo -e "${GREEN}✅ All services started!${NC}"
echo ""
echo "📱 Laravel App:    http://localhost:8000"
echo "📡 Node.js API:    http://localhost:3000"
echo "📊 API Health:     http://localhost:3000/health"
echo "📝 API Logs:       http://localhost:3000/api/logs"
echo "🔗 n8n Dashboard:  http://localhost:5678"
echo ""
echo -e "${YELLOW}⚠️  n8n will take a moment to start up...${NC}"
echo ""
echo "Press Ctrl+C to stop all services"

# Wait for Ctrl+C
trap "echo 'Stopping services...'; kill $NODE_PID $LARAVEL_PID $N8N_PID 2>/dev/null; exit" INT
wait
