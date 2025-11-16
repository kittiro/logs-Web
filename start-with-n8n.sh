#!/bin/bash

echo "🚀 Starting WebManga with n8n and LINE Bot..."

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
PURPLE='\033[0;35m'
NC='\033[0m' # No Color

# Check if n8n is available
if ! command -v n8n &> /dev/null && ! command -v npx &> /dev/null; then
    echo -e "${YELLOW}⚠️  n8n not found. Install with: npm install -g n8n${NC}"
fi

# Start Node.js API in background
echo -e "${BLUE}📡 Starting Node.js Log API on port 3000...${NC}"
cd node-api && npm start &
NODE_PID=$!

# Wait for Node.js to start
sleep 2

# Start Laravel
echo -e "${GREEN}🎨 Starting Laravel application on port 8000...${NC}"
cd ..
php artisan serve --host=0.0.0.0 --port=8000 &
LARAVEL_PID=$!

# Wait for Laravel to start
sleep 2

# Start n8n
echo -e "${PURPLE}🔗 Starting n8n on port 5678...${NC}"
export N8N_SECURE_COOKIE=false
export N8N_HOST=localhost
export N8N_PORT=5678
export N8N_PROTOCOL=http

if command -v n8n &> /dev/null; then
    n8n start &
    N8N_PID=$!
else
    npx n8n &
    N8N_PID=$!
fi

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
echo -e "${BLUE}📚 Next Steps:${NC}"
echo "   1. Open n8n: http://localhost:5678"
echo "   2. Import workflows from: node-api/n8n-workflows/"
echo "   3. Read guide: node-api/N8N_COMPLETE_GUIDE.md"
echo ""
echo "Press Ctrl+C to stop all services"

# Cleanup function
cleanup() {
    echo ""
    echo -e "${YELLOW}🛑 Stopping services...${NC}"
    kill $NODE_PID $LARAVEL_PID $N8N_PID 2>/dev/null
    echo -e "${GREEN}✅ All services stopped${NC}"
    exit 0
}

# Wait for Ctrl+C
trap cleanup INT TERM
wait
