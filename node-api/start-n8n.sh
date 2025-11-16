#!/bin/bash

echo "🔗 Starting n8n for local development..."
echo ""

# Set environment variables for local development
export N8N_SECURE_COOKIE=false
export N8N_HOST=localhost
export N8N_PORT=5678
export N8N_PROTOCOL=http

# Optional: Set data directory
# export N8N_USER_FOLDER=~/.n8n

echo "⚙️  Configuration:"
echo "   - Secure Cookie: disabled (for local dev)"
echo "   - Host: localhost"
echo "   - Port: 5678"
echo "   - Protocol: http"
echo ""

# Check if n8n is installed
if ! command -v n8n &> /dev/null && ! command -v npx &> /dev/null; then
    echo "❌ n8n not found!"
    echo ""
    echo "Install with one of these methods:"
    echo "  1. npm install -g n8n"
    echo "  2. Use npx (will be used automatically)"
    echo "  3. Use Docker: docker run -it --rm --name n8n -p 5678:5678 -e N8N_SECURE_COOKIE=false n8nio/n8n"
    echo ""
    exit 1
fi

echo "🚀 Starting n8n..."
echo ""
echo "📍 n8n Dashboard: http://localhost:5678"
echo ""
echo "📚 Next Steps:"
echo "   1. Open http://localhost:5678 in your browser"
echo "   2. Create an account (first time only)"
echo "   3. Import workflows from: n8n-workflows/"
echo "   4. Read guide: N8N_COMPLETE_GUIDE.md"
echo ""
echo "Press Ctrl+C to stop n8n"
echo ""

# Start n8n
if command -v n8n &> /dev/null; then
    n8n start
else
    npx n8n
fi
