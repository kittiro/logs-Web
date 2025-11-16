#!/bin/bash

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m'

API_URL="http://localhost:3000"

echo -e "${BLUE}🧪 Testing WebManga Log API...${NC}"
echo ""

# Test 1: Health Check
echo -e "${YELLOW}1. Testing Health Check...${NC}"
curl -s "${API_URL}/health" | jq '.'
echo ""

# Test 2: Get Available Dates
echo -e "${YELLOW}2. Testing Get Available Dates...${NC}"
curl -s "${API_URL}/api/logs/dates" | jq '.'
echo ""

# Test 3: Get Logs
echo -e "${YELLOW}3. Testing Get Logs (limit 5)...${NC}"
curl -s "${API_URL}/api/logs?limit=5" | jq '.'
echo ""

# Test 4: Get Stats
echo -e "${YELLOW}4. Testing Get Statistics...${NC}"
curl -s "${API_URL}/api/logs/stats" | jq '.'
echo ""

# Test 5: Filter by IP
echo -e "${YELLOW}5. Testing Filter by IP...${NC}"
curl -s "${API_URL}/api/logs?ip=127.0.0.1&limit=3" | jq '.'
echo ""

# Test 6: n8n Webhook
echo -e "${YELLOW}6. Testing n8n Webhook...${NC}"
curl -s -X POST "${API_URL}/api/webhook/n8n" \
  -H "Content-Type: application/json" \
  -d '{"action":"getStats","filters":{"date":"2025-09-25"}}' | jq '.'
echo ""

echo -e "${GREEN}✅ All tests completed!${NC}"
